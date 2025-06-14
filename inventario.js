document.addEventListener('DOMContentLoaded', () => {
    // --- SELECCIÓN DE ELEMENTOS DEL DOM ---
    const form = document.getElementById('equipo-form');
    const formTitle = document.getElementById('form-title');
    const tableBody = document.getElementById('equipos-table-body');
    const noRecordsMessage = document.getElementById('no-records');
    const downloadPdfBtn = document.getElementById('download-inventory-pdf');
    const submitBtn = document.getElementById('submit-btn');
    const cancelEditBtn = document.getElementById('cancel-edit-btn');
    const recordIdInput = document.getElementById('record-id');

    // --- FUNCIÓN PARA MOSTRAR ESTADO DE CUMPLIMIENTO ---
    const getComplianceStatus = (record) => {
        const allLicensesOk = Object.values(record.licencias).every(lic => lic === 'si');
        if (allLicensesOk || !record.compliance_due_date) {
            return { color: 'bg-green-100 text-green-800', message: 'En Regla' };
        }

        const deadline = new Date(record.compliance_due_date);
        const today = new Date();
        deadline.setUTCHours(0, 0, 0, 0);
        today.setHours(0, 0, 0, 0);
        
        const diffTime = deadline - today;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        if (diffDays < 0) return { color: 'bg-red-100 text-red-800', message: 'Vencido' };
        if (diffDays <= 7) return { color: 'bg-yellow-100 text-yellow-800', message: `Vence en ${diffDays} día(s)` };
        return { color: 'bg-blue-100 text-blue-800', message: `Vence en ${diffDays} días` };
    };
    
    // --- FUNCIÓN PARA OBTENER Y MOSTRAR DATOS DE LA BD ---
    const fetchAndRenderTable = async () => {
        try {
            const response = await fetch('./api/get_inventory.php');
            const records = await response.json();

            tableBody.innerHTML = '';
            noRecordsMessage.style.display = records.length === 0 ? 'block' : 'none';

            records.forEach(record => {
                const row = document.createElement('tr');
                const { color, message } = getComplianceStatus(record);

                row.innerHTML = `
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">${record.employee_name}</div>
                        <div class="text-sm text-indigo-600">${record.employee_role || ''}</div>
                        <div class="text-sm text-gray-500 mt-1">${record.employee_email}</div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">${record.brand} (${record.equipment_type})</div>
                        <div class="text-sm text-gray-500">IP: ${record.ip_address}</div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div>Win: <span class="font-semibold ${record.licencias.windows === 'si' ? 'text-green-600' : 'text-red-600'}">${record.licencias.windows.toUpperCase()}</span></div>
                        <div>Office: <span class="font-semibold ${record.licencias.office === 'si' ? 'text-green-600' : 'text-red-600'}">${record.licencias.office.toUpperCase()}</span></div>
                        <div>AV: <span class="font-semibold ${record.licencias.antivirus === 'si' ? 'text-green-600' : 'text-red-600'}">${record.licencias.antivirus.toUpperCase()}</span></div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${color}">${message}</span>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                        <button class="text-indigo-600 hover:text-indigo-900" data-id="${record.id}" data-action="edit">Editar</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        } catch (error) {
            console.error('Error al cargar los datos:', error);
        }
    };

    // --- FUNCIÓN PARA RESETEAR EL FORMULARIO ---
    const resetForm = () => {
        form.reset();
        recordIdInput.value = '';
        formTitle.textContent = 'Nuevo Registro de Equipo';
        submitBtn.textContent = 'Guardar Registro';
        cancelEditBtn.classList.add('hidden');
    };

    // --- EVENTO PARA GUARDAR O ACTUALIZAR UN REGISTRO ---
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const recordData = {
            id: recordIdInput.value,
            funcionario: document.getElementById('funcionario').value,
            cargo: document.getElementById('employee-role').value,
            celular: document.getElementById('celular').value,
            correo: document.getElementById('correo').value,
            tipoEquipo: document.getElementById('tipo-equipo').value,
            marca: document.getElementById('marca').value,
            ip: document.getElementById('ip').value,
            mac: document.getElementById('mac').value,
            licencias: {
                windows: document.querySelector('input[name="windows"]:checked').value,
                office: document.querySelector('input[name="office"]:checked').value,
                antivirus: document.querySelector('input[name="antivirus"]:checked').value,
            }
        };

        const isUpdating = !!recordData.id;
        const url = isUpdating ? './api/update_inventory.php' : './api/add_inventory.php';

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(recordData)
            });
            const result = await response.json();
            if (result.success) {
                resetForm();
                fetchAndRenderTable();
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            console.error('Error al guardar:', error);
            alert('Hubo un error de conexión al guardar el registro.');
        }
    });

    // --- EVENTO PARA EL BOTON DE EDITAR EN LA TABLA ---
    tableBody.addEventListener('click', async (e) => {
        const action = e.target.dataset.action;
        const id = e.target.dataset.id;
        if (action !== 'edit') return;

        // Usamos una consulta al API para obtener los datos más frescos del registro
        const response = await fetch('./api/get_inventory.php');
        const records = await response.json();
        const recordToEdit = records.find(r => r.id == id); 

        if (recordToEdit) {
            formTitle.textContent = 'Editando Registro de Equipo';
            submitBtn.textContent = 'Actualizar Registro';
            cancelEditBtn.classList.remove('hidden');

            recordIdInput.value = recordToEdit.id;
            document.getElementById('funcionario').value = recordToEdit.employee_name;
            document.getElementById('employee-role').value = recordToEdit.employee_role;
            document.getElementById('celular').value = recordToEdit.employee_phone || '';
            document.getElementById('correo').value = recordToEdit.employee_email;
            document.getElementById('tipo-equipo').value = recordToEdit.equipment_type;
            document.getElementById('marca').value = recordToEdit.brand;
            document.getElementById('ip').value = recordToEdit.ip_address;
            document.getElementById('mac').value = recordToEdit.mac_address || '';
            
            document.querySelector(`input[name="windows"][value="${recordToEdit.licencias.windows}"]`).checked = true;
            document.querySelector(`input[name="office"][value="${recordToEdit.licencias.office}"]`).checked = true;
            document.querySelector(`input[name="antivirus"][value="${recordToEdit.licencias.antivirus}"]`).checked = true;
            
            window.scrollTo(0, 0);
        }
    });
    
    // --- EVENTO PARA CANCELAR LA EDICIÓN ---
    cancelEditBtn.addEventListener('click', resetForm);
    
    // --- LÓGICA PARA DESCARGAR EL PDF ---
    downloadPdfBtn.addEventListener('click', async () => {
        try {
            const response = await fetch('./api/get_inventory.php');
            const records = await response.json();

            if (records.length === 0) {
                alert('No hay registros para exportar en el PDF.');
                return;
            }
            
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF({ orientation: 'l', unit: 'mm', format: 'a4' });
            
            const head = [['Funcionario', 'Cargo', 'Contacto', 'Equipo', 'Red', 'Licencias', 'Estado']];
            const body = records.map(record => {
                const { message } = getComplianceStatus(record);
                const licenciasStr = `Win: ${record.licencias.windows.toUpperCase()}, Office: ${record.licencias.office.toUpperCase()}, AV: ${record.licencias.antivirus.toUpperCase()}`;
                
                return [
                    record.employee_name || 'N/A',
                    record.employee_role || 'N/A',
                    `${record.employee_email || 'N/A'}`,
                    `${record.brand || 'N/A'} (${record.equipment_type || 'N/A'})`,
                    `IP: ${record.ip_address || 'N/A'}`,
                    licenciasStr,
                    message
                ];
            });

            pdf.setFontSize(18).text('Informe de Inventario de Equipos', 148, 15, { align: 'center' });
            pdf.setFontSize(12).text('Hospital Universitario Hernando Moncaleano Perdomo', 148, 22, { align: 'center' });

            pdf.autoTable({
                startY: 30,
                head: head,
                body: body,
                theme: 'grid',
                headStyles: { fillColor: [22, 163, 74] },
                styles: { fontSize: 8 },
                columnStyles: {
                    0: { cellWidth: 40 },
                    1: { cellWidth: 40 },
                    2: { cellWidth: 45 },
                    3: { cellWidth: 35 },
                    4: { cellWidth: 35 },
                    5: { cellWidth: 55 },
                    6: { cellWidth: 20 }
                }
            });
            
            pdf.save('Informe_Inventario_Equipos.pdf');

        } catch (error) {
            console.error("Error al generar el PDF:", error);
            alert("Ocurrió un error al generar el PDF. Revisa la consola para más detalles.");
        }
    });

    // --- Carga inicial de los datos al entrar a la página ---
    fetchAndRenderTable();
});
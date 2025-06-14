document.addEventListener('DOMContentLoaded', () => {
    Chart.register(ChartDataLabels);

    // --- SELECCIÓN DE ELEMENTOS DEL DOM ---
    const form = document.getElementById('personal-form');
    const tipoUsuarioSelect = document.getElementById('tipo-usuario');
    const estudianteContainer = document.getElementById('estudiante-container');
    const residenteContainer = document.getElementById('residente-container');
    const externoContainer = document.getElementById('externo-container');
    const auditorContainer = document.getElementById('auditor-container');
    const departamentoContainer = document.getElementById('departamento-container');

    const analysisTextarea = document.getElementById('analysis-text');
    const addAnalysisBtn = document.getElementById('add-analysis-btn');
    const analysisDisplayContainer = document.getElementById('analysis-display-container');
    const analysisDisplayText = document.getElementById('analysis-display-text');
    const tableBody = document.getElementById('personal-table-body');
    const noDataMessage = document.getElementById('no-data');
    const downloadPdfButton = document.getElementById('download-pdf');
    const saveReportBtn = document.getElementById('save-report-btn');
    const savedReportsContainer = document.getElementById('saved-reports-container');
    const noSavedReports = document.getElementById('no-saved-reports');
    
    const tiposChartContainer = document.getElementById('tipos-chart-container');
    const estudiantesChartContainer = document.getElementById('estudiantes-chart-container');
    const residentesChartContainer = document.getElementById('residentes-chart-container');
    const externosChartContainer = document.getElementById('externos-chart-container');
    const auditoresChartContainer = document.getElementById('auditores-chart-container');

    // --- VARIABLES Y ESTADO DE LA APLICACIÓN ---
    let personalData = [];
    let tiposChartInstance, estudiantesChartInstance, residentesChartInstance, externosChartInstance, auditoresChartInstance;
    const chartColors = ['#4338CA', '#16A34A', '#F97316', '#DC2626', '#2563EB', '#7C3AED', '#DB2777', '#4B5563'];
    
    const rolesDeDepartamento = [
        "Medico General", "Especialista", "Jefe de Enfermería", "Auxiliar de Laboratorio",
        "Analista de Facturación", "Facturación Cirugía", "Auditores Administrativos",
        "Administrador", "Rol Administrador", "Interno"
    ];

    // --- FUNCIONES DE RENDERIZADO Y LÓGICA DEL FRONT-END ---

    const renderTable = () => {
        tableBody.innerHTML = '';
        noDataMessage.style.display = personalData.length === 0 ? 'block' : 'none';
        tableBody.classList.toggle('hidden', personalData.length === 0);

        personalData.forEach((item, index) => {
            const row = document.createElement('tr');
            let detalles = item.detalles || 'N/A';
            
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${item.tipo}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.cantidad}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${detalles}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button class="text-red-600 hover:text-red-900" data-index="${index}">Eliminar</button>
                </td>
            `;
            tableBody.appendChild(row);
        });
    };

    const updateCharts = () => {
        if(tiposChartInstance) tiposChartInstance.destroy();
        if(estudiantesChartInstance) estudiantesChartInstance.destroy();
        if(residentesChartInstance) residentesChartInstance.destroy();
        if(externosChartInstance) externosChartInstance.destroy();
        if(auditoresChartInstance) auditoresChartInstance.destroy();

        const tiposData = personalData.reduce((acc, item) => { acc[item.tipo] = (acc[item.tipo] || 0) + parseInt(item.cantidad, 10); return acc; }, {});
        tiposChartContainer.classList.toggle('hidden', Object.keys(tiposData).length === 0);
        if (Object.keys(tiposData).length > 0) {
          tiposChartInstance = new Chart(document.getElementById('tipos-chart'), { type: 'bar', data: { labels: Object.keys(tiposData), datasets: [{ label: 'Cantidad', data: Object.values(tiposData), backgroundColor: chartColors }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } } });
        }

        const estudiantesData = personalData.filter(p => p.tipo === 'Estudiante' || p.tipo === 'Estudiantes de Enfermería').reduce((acc, item) => { acc[item.universidad] = (acc[item.universidad] || 0) + parseInt(item.cantidad, 10); return acc; }, {});
        estudiantesChartContainer.classList.toggle('hidden', Object.keys(estudiantesData).length === 0);
        if (Object.keys(estudiantesData).length > 0) {
            estudiantesChartInstance = new Chart(document.getElementById('estudiantes-chart'), { type: 'doughnut', data: { labels: Object.keys(estudiantesData), datasets: [{ data: Object.values(estudiantesData), backgroundColor: chartColors }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top' }, datalabels: { formatter: (v, ctx) => (v / ctx.chart.data.datasets[0].data.reduce((s, d) => s + d, 0) * 100).toFixed(1) + '%', color: '#fff', font: { weight: 'bold', size: 14 } } } } });
        }
        
        const residentesData = personalData.filter(p => p.tipo === 'Residente').reduce((acc, item) => { acc[item.universidad] = (acc[item.universidad] || 0) + parseInt(item.cantidad, 10); return acc; }, {});
        residentesChartContainer.classList.toggle('hidden', Object.keys(residentesData).length === 0);
        if (Object.keys(residentesData).length > 0) {
            residentesChartInstance = new Chart(document.getElementById('residentes-chart'), { type: 'bar', data: { labels: Object.keys(residentesData), datasets: [{ label: 'Cantidad', data: Object.values(residentesData), backgroundColor: chartColors.slice(1) }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } } });
        }
        
        const externosData = personalData.filter(p => p.tipo === 'Externo').reduce((acc, item) => { const key = `${item.area} - ${item.servicio}`; acc[key] = (acc[key] || 0) + parseInt(item.cantidad, 10); return acc; }, {});
        externosChartContainer.classList.toggle('hidden', Object.keys(externosData).length === 0);
        if (Object.keys(externosData).length > 0) {
            externosChartInstance = new Chart(document.getElementById('externos-chart'), { type: 'bar', data: { labels: Object.keys(externosData), datasets: [{ label: 'Cantidad', data: Object.values(externosData), backgroundColor: chartColors.slice(2) }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } } });
        }

        const auditoresData = personalData.filter(p => p.tipo === 'Auditor Externo').reduce((acc, item) => { acc[item.eps] = (acc[item.eps] || 0) + parseInt(item.cantidad, 10); return acc; }, {});
        auditoresChartContainer.classList.toggle('hidden', Object.keys(auditoresData).length === 0);
        if (Object.keys(auditoresData).length > 0) {
            auditoresChartInstance = new Chart(document.getElementById('auditores-chart'), { type: 'bar', data: { labels: Object.keys(auditoresData), datasets: [{ label: 'Cantidad', data: Object.values(auditoresData), backgroundColor: chartColors.slice(3) }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } } });
        }
    };

    const toggleFields = () => {
        const selection = tipoUsuarioSelect.value;
        
        estudianteContainer.classList.add('hidden');
        residenteContainer.classList.add('hidden');
        externoContainer.classList.add('hidden');
        auditorContainer.classList.add('hidden');
        departamentoContainer.classList.add('hidden');

        document.querySelectorAll('#personal-form input[required]').forEach(input => input.required = false);

        if (selection === 'Estudiante' || selection === 'Estudiantes de Enfermería') {
            estudianteContainer.classList.remove('hidden');
            document.getElementById('universidad-estudiante').required = true;
        } else if (selection === 'Residente') {
            residenteContainer.classList.remove('hidden');
            document.getElementById('universidad-residente').required = true;
            document.getElementById('duracion-residencia').required = true;
        } else if (selection === 'Externo') {
            externoContainer.classList.remove('hidden');
            document.getElementById('area-hospital').required = true;
            document.getElementById('tipo-servicio').required = true;
        } else if (selection === 'Auditor Externo') {
            auditorContainer.classList.remove('hidden');
            document.getElementById('eps-auditor').required = true;
        } else if (rolesDeDepartamento.includes(selection)) {
            departamentoContainer.classList.remove('hidden');
            document.getElementById('departamento').required = true;
        }
    };
    
    // --- EVENT LISTENERS (BOTONES Y FORMULARIOS) ---
    
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const tipo = tipoUsuarioSelect.value;
        const cantidad = parseInt(document.getElementById('cantidad').value, 10);
        let newItem = { tipo, cantidad };
        let detalles = '';

        if (tipo === 'Estudiante' || tipo === 'Estudiantes de Enfermería') {
            newItem.universidad = document.getElementById('universidad-estudiante').value || 'No especificada';
            detalles = `U: ${newItem.universidad}`;
        } else if (tipo === 'Residente') {
            newItem.universidad = document.getElementById('universidad-residente').value || 'No especificada';
            newItem.duracion = parseInt(document.getElementById('duracion-residencia').value, 10) || 0;
            detalles = `U: ${newItem.universidad}, Duración: ${newItem.duracion} meses`;
        } else if (tipo === 'Externo') {
            newItem.area = document.getElementById('area-hospital').value || 'No especificada';
            newItem.servicio = document.getElementById('tipo-servicio').value || 'No especificado';
            detalles = `Área: ${newItem.area}, Servicio: ${newItem.servicio}`;
        } else if (tipo === 'Auditor Externo') {
            newItem.eps = document.getElementById('eps-auditor').value || 'No especificada';
            detalles = `EPS: ${newItem.eps}`;
        } else if (rolesDeDepartamento.includes(tipo)) {
            newItem.area = document.getElementById('departamento').value || 'No especificado';
            detalles = `Dpto: ${newItem.area}`;
        }
        
        newItem.detalles = detalles;
        personalData.push(newItem);
        renderTable();
        updateCharts();
        form.reset();
        toggleFields();
    });

    tableBody.addEventListener('click', (e) => {
        if (e.target.tagName === 'BUTTON' && e.target.dataset.index) {
            personalData.splice(parseInt(e.target.dataset.index, 10), 1);
            renderTable();
            updateCharts();
        }
    });

    addAnalysisBtn.addEventListener('click', () => {
        const analysisText = analysisTextarea.value;
        analysisDisplayText.textContent = analysisText;
        analysisDisplayContainer.classList.toggle('hidden', analysisText.trim() === '');
    });

    downloadPdfButton.addEventListener('click', () => {
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF({ orientation: 'p', unit: 'mm', format: 'a4' });
        const margin = 15;
        const pdfWidth = pdf.internal.pageSize.getWidth();
        let cursorY = margin;

        pdf.setFontSize(18).setFont('helvetica', 'bold').text('Hospital Universitario Hernando Moncaleano Perdomo', pdfWidth / 2, cursorY, { align: 'center' });
        cursorY += 8;
        pdf.setFontSize(12).setFont('helvetica', 'normal').text('Informe de Indicadores de Personal', pdfWidth / 2, cursorY, { align: 'center' });
        cursorY += 15;

        const analysis = analysisDisplayText.textContent;
        if (analysis.trim() !== '') {
            pdf.setFontSize(14).setFont('helvetica', 'bold').text('Análisis del Informe', margin, cursorY);
            cursorY += 6;
            pdf.setFontSize(11).setFont('helvetica', 'normal').text(pdf.splitTextToSize(analysis, pdfWidth - (margin * 2)), margin, cursorY);
            cursorY += (pdf.splitTextToSize(analysis, pdfWidth - (margin * 2)).length * 5) + 10;
        }

        const addChartToPdf = (chartInstance, title) => {
            if (chartInstance && chartInstance.canvas.offsetParent !== null) {
                const chartHeight = 82; 
                if (cursorY + chartHeight > pdf.internal.pageSize.getHeight() - margin) {
                    pdf.addPage();
                    cursorY = margin;
                }
                pdf.setFontSize(14).setFont('helvetica', 'bold').text(title, pdfWidth / 2, cursorY, { align: 'center' });
                cursorY += 6;
                const chartImg = chartInstance.canvas.toDataURL('image/png', 1.0);
                pdf.addImage(chartImg, 'PNG', margin, cursorY, pdfWidth - (margin * 2), chartHeight);
                cursorY += chartHeight + 12;
            }
        };
        
        addChartToPdf(tiposChartInstance, 'Distribución General por Tipo');
        addChartToPdf(estudiantesChartInstance, 'Estudiantes por Universidad');
        addChartToPdf(residentesChartInstance, 'Residentes por Universidad');
        addChartToPdf(externosChartInstance, 'Externos por Área y Servicio');
        addChartToPdf(auditoresChartInstance, 'Auditores Externos por EPS');

        if (cursorY + 20 > pdf.internal.pageSize.getHeight() - margin) {
            pdf.addPage();
            cursorY = margin;
        }
         if (personalData.length > 0) {
             pdf.autoTable({ startY: cursorY, head: [['Tipo', 'Cantidad', 'Detalles']], body: personalData.map(item => [item.tipo, item.cantidad, item.detalles || 'N/A']), theme: 'grid', headStyles: { fillColor: [67, 56, 202] }, margin: { left: margin, right: margin } });
        } else {
            pdf.text('No hay datos registrados.', margin, cursorY);
        }

        pdf.save('Informe_Indicadores_Hospital.pdf');
    });

    tipoUsuarioSelect.addEventListener('change', toggleFields);

    // --- FUNCIONES CONECTADAS A LA BASE DE DATOS ---

    const fetchAndRenderSavedReports = async () => {
        try {
            const response = await fetch('./api/get_saved_reports.php');
            const reports = await response.json();
            
            savedReportsContainer.innerHTML = '';
            noSavedReports.style.display = reports.length === 0 ? 'block' : 'none';

            reports.forEach(report => {
                const reportEl = document.createElement('div');
                reportEl.className = 'flex justify-between items-center p-2 border rounded-md';
                const reportDate = new Date(report.created_at).toLocaleString('es-CO');
                reportEl.innerHTML = `
                    <span class="text-sm">${reportDate}</span>
                    <div class="space-x-2">
                        <button class="text-sm bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600" data-id="${report.id}" data-action="load">Cargar</button>
                    </div>
                `;
                savedReportsContainer.appendChild(reportEl);
            });
        } catch(error) {
            console.error('Error cargando informes guardados:', error);
        }
    };

    saveReportBtn.addEventListener('click', async () => {
        if (personalData.length === 0 && analysisTextarea.value.trim() === '') {
            alert('No hay datos ni análisis para guardar en el informe.');
            return;
        }
        const reportPayload = { analysis: analysisTextarea.value, data: personalData };
        try {
            const response = await fetch('./api/save_report.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(reportPayload)
            });
            const result = await response.json();
            if (result.success) {
                alert(result.message);
                fetchAndRenderSavedReports();
                personalData = [];
                analysisTextarea.value = '';
                addAnalysisBtn.click();
                renderTable();
                updateCharts();
            } else {
                alert('Error al guardar el informe: ' + result.message);
            }
        } catch(error) {
            console.error('Error de conexión al guardar:', error);
        }
    });

    savedReportsContainer.addEventListener('click', async (e) => {
        const target = e.target;
        if(target.tagName !== 'BUTTON' || target.dataset.action !== 'load') return;
        
        const reportId = target.dataset.id;
        
        try {
            const response = await fetch(`./api/load_report.php?id=${reportId}`);
            const result = await response.json();
            if(result.success) {
                personalData = result.data || [];
                // Reconstruimos los detalles para la tabla al cargar
                personalData.forEach(item => {
                    if (item.tipo === 'Estudiante' || item.tipo === 'Estudiantes de Enfermería') {
                        item.detalles = `U: ${item.universidad}`;
                    } else if (item.tipo === 'Residente') {
                        item.detalles = `U: ${item.universidad}, Duración: ${item.duracion} meses`;
                    } else if (item.tipo === 'Externo') {
                        item.detalles = `Área: ${item.area}, Servicio: ${item.servicio}`;
                    } else if (item.tipo === 'Auditor Externo') {
                        item.detalles = `EPS: ${item.eps}`;
                    } else if (rolesDeDepartamento.includes(item.tipo)) {
                        item.detalles = `Dpto: ${item.area}`;
                    }
                });
                analysisTextarea.value = result.analysis || '';
                addAnalysisBtn.click();
                renderTable();
                updateCharts();
            } else {
                alert('Error al cargar el informe.');
            }
        } catch(error) {
            console.error('Error cargando informe:', error);
        }
    });
    
    // --- INICIALIZACIÓN DE LA PÁGINA ---
    toggleFields();
    renderTable();
    updateCharts();
    fetchAndRenderSavedReports();
});
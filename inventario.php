<?php
// Iniciar la sesión
session_start();

// Si el usuario no ha iniciado sesión, redirigirlo a la página de inicio.
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.html");
    exit;
}

// Encabezados para prevenir que el navegador guarde la página en caché.
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario de Equipos - Hospital Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-gray-100 text-gray-800">

    <nav class="bg-white shadow-md">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="portal.php" class="flex-shrink-0">
                    <img src="https://th.bing.com/th/id/OIP._0ZqxGqmntFwJM-PBN8W2gAAAA?rs=1&pid=ImgDetMain" alt="Logo Hospital" class="h-14 w-auto">
                </a>
                <div>
                    <a href="api/logout.php" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-300 flex items-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Cerrar Sesión</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="container mx-auto p-4 md:p-8">
        <header class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Registro de Información de Computadoras</h1>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <h2 id="form-title" class="text-xl font-semibold mb-4 border-b pb-2">Nuevo Registro de Equipo</h2>
                    <form id="equipo-form" class="space-y-4">
                        <input type="hidden" id="record-id">
                        
                        <div>
                            <label for="funcionario" class="block text-sm font-medium text-gray-700">Nombre del Funcionario</label>
                            <input type="text" id="funcionario" class="mt-1 block w-full input-style" required>
                        </div>
                        <div>
                            <label for="employee-role" class="block text-sm font-medium text-gray-700">Cargo / Rol</label>
                            <select id="employee-role" class="mt-1 block w-full input-style" required>
                                <option>Residente</option>
                                <option>Interno</option>
                                <option>Estudiantes de Enfermería</option>
                                <option>Medico General</option>
                                <option>Especialista</option>
                                <option>Jefe de Enfermería</option>
                                <option>Auxiliar de Laboratorio</option>
                                <option>Analista de Facturación</option>
                                <option>Facturación Cirugía</option>
                                <option>Auditores Externos</option>
                                <option>Auditores Administrativos</option>
                                <option>Administrador</option>
                                <option>Rol Administrador</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="celular" class="block text-sm font-medium text-gray-700">Celular</label>
                                <input type="tel" id="celular" class="mt-1 block w-full input-style">
                            </div>
                            <div>
                                <label for="correo" class="block text-sm font-medium text-gray-700">Correo</label>
                                <input type="email" id="correo" class="mt-1 block w-full input-style" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="tipo-equipo" class="block text-sm font-medium text-gray-700">Tipo de Equipo</label>
                                <select id="tipo-equipo" class="mt-1 block w-full input-style">
                                    <option>PC</option>
                                    <option>Portátil</option>
                                </select>
                            </div>
                            <div>
                                <label for="marca" class="block text-sm font-medium text-gray-700">Marca</label>
                                <input type="text" id="marca" class="mt-1 block w-full input-style">
                            </div>
                        </div>
                         <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="ip" class="block text-sm font-medium text-gray-700">Dirección IP</label>
                                <input type="text" id="ip" class="mt-1 block w-full input-style">
                            </div>
                             <div>
                                <label for="mac" class="block text-sm font-medium text-gray-700">Dirección MAC</label>
                                <input type="text" id="mac" class="mt-1 block w-full input-style">
                            </div>
                        </div>
                        <fieldset class="space-y-2">
                            <legend class="text-sm font-medium text-gray-700">Estado de Licencias</legend>
                            <div class="flex justify-between items-center">
                                <label>Antivirus</label>
                                <div class="flex gap-4" id="antivirus">
                                    <label><input type="radio" name="antivirus" value="si" checked> Si</label>
                                    <label><input type="radio" name="antivirus" value="no"> No</label>
                                </div>
                            </div>
                            <div class="flex justify-between items-center">
                                <label>Office</label>
                                <div class="flex gap-4" id="office">
                                    <label><input type="radio" name="office" value="si" checked> Si</label>
                                    <label><input type="radio" name="office" value="no"> No</label>
                                </div>
                            </div>
                            <div class="flex justify-between items-center">
                                <label>Windows</label>
                                <div class="flex gap-4" id="windows">
                                    <label><input type="radio" name="windows" value="si" checked> Si</label>
                                    <label><input type="radio" name="windows" value="no"> No</label>
                                </div>
                            </div>
                        </fieldset>

                        <div class="pt-2 flex flex-col space-y-2">
                            <button type="submit" id="submit-btn" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-300">Guardar Registro</button>
                            <button type="button" id="cancel-edit-btn" class="w-full bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-300 hidden">Cancelar Edición</button>
                        </div>
                    </form>
                </div>
                 <div class="bg-white p-6 rounded-xl shadow-md space-y-4">
                     <h2 class="text-xl font-semibold mb-2 border-b pb-2">Acciones</h2>
                     <button id="download-inventory-pdf" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-300">Descargar Inventario en PDF</button>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white p-6 rounded-xl shadow-md">
                     <h2 class="text-xl font-semibold mb-4">Equipos Registrados</h2>
                     <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Funcionario</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Equipo</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Licencias</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado Cumplimiento</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="equipos-table-body" class="bg-white divide-y divide-gray-200">
                                </tbody>
                        </table>
                        <p id="no-records" class="text-center text-gray-500 py-6">No hay equipos registrados.</p>
                     </div>
                </div>
            </div>
        </div>
    </main>

    <div id="view-modal" class="fixed inset-0 bg-gray-800 bg-opacity-75 overflow-y-auto h-full w-full flex items-center justify-center hidden">
        <div class="relative mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="text-2xl font-bold text-gray-900">Detalles del Equipo</h3>
                <button id="close-modal-btn" class="text-gray-600 hover:text-gray-900 text-2xl font-bold">&times;</button>
            </div>
            <div id="modal-content" class="mt-4 space-y-4">
                </div>
        </div>
    </div>

    <script src="inventario.js" defer></script>
</body>
</html>
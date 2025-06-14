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
    <title>Indicadores Hospital Universitario</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
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
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <h2 class="text-xl font-semibold mb-4 border-b pb-2">Panel de Control</h2>
                    <form id="personal-form" class="space-y-4">
                        <div>
                            <label for="tipo-usuario" class="block text-sm font-medium text-gray-700">Tipo de Usuario</label>
                            <select id="tipo-usuario" name="tipo-usuario" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <optgroup label="Personal Asistencial">
                                    <option>Medico General</option>
                                    <option>Especialista</option>
                                    <option>Jefe de Enfermería</option>
                                    <option>Auxiliar de Laboratorio</option>
                                </optgroup>
                                <optgroup label="Personal Administrativo">
                                    <option>Analista de Facturación</option>
                                    <option>Facturación Cirugía</option>
                                    <option>Auditores Administrativos</option>
                                    <option>Administrador</option>
                                    <option>Rol Administrador</option>
                                </optgroup>
                                <optgroup label="Personal en Formación">
                                    <option>Estudiante</option>
                                    <option>Estudiantes de Enfermería</option>
                                    <option>Residente</option>
                                    <option>Interno</option>
                                </optgroup>
                                <optgroup label="Personal Externo">
                                    <option>Auditor Externo</option>
                                    <option>Externo</option>
                                </optgroup>
                            </select>
                        </div>
                        <div id="estudiante-container" class="hidden space-y-4">
                             <div>
                                <label for="universidad-estudiante" class="block text-sm font-medium text-gray-700">Universidad del Estudiante</label>
                                <input type="text" id="universidad-estudiante" placeholder="Nombre de la universidad" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm">
                            </div>
                        </div>

                        <div id="departamento-container" class="hidden space-y-4">
                             <div>
                                <label for="departamento" class="block text-sm font-medium text-gray-700">Área / Departamento</label>
                                <input type="text" id="departamento" placeholder="Ej: Urgencias, Facturación" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm">
                            </div>
                        </div>

                        <div id="residente-container" class="hidden space-y-4">
                            <div>
                                <label for="universidad-residente" class="block text-sm font-medium text-gray-700">Universidad del Residente</label>
                                <input type="text" id="universidad-residente" placeholder="Nombre de la universidad" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm">
                            </div>
                             <div>
                                <label for="duracion-residencia" class="block text-sm font-medium text-gray-700">Duración de Residencia (meses)</label>
                                <input type="number" id="duracion-residencia" min="1" placeholder="Ej: 36" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm">
                            </div>
                        </div>
                        <div id="externo-container" class="hidden space-y-4">
                            <div>
                                <label for="area-hospital" class="block text-sm font-medium text-gray-700">Área del Hospital</label>
                                <input type="text" id="area-hospital" placeholder="Ej: Urgencias, UCI" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm">
                            </div>
                             <div>
                                <label for="tipo-servicio" class="block text-sm font-medium text-gray-700">Tipo de Servicio</label>
                                <input type="text" id="tipo-servicio" placeholder="Ej: Mantenimiento, Pediatría" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm">
                            </div>
                        </div>
                        <div id="auditor-container" class="hidden space-y-4">
                            <div>
                                <label for="eps-auditor" class="block text-sm font-medium text-gray-700">EPS de Origen</label>
                                <input type="text" id="eps-auditor" placeholder="Nombre de la EPS" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm">
                            </div>
                        </div>
                        <div>
                            <label for="cantidad" class="block text-sm font-medium text-gray-700">Cantidad</label>
                            <input type="number" id="cantidad" name="cantidad" min="1" value="1" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm" required>
                        </div>
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-300">
                            Agregar Registro
                        </button>
                    </form>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-md">
                    <h2 class="text-xl font-semibold mb-4 border-b pb-2">Análisis del Informe</h2>
                    <textarea id="analysis-text" rows="6" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Escribe aquí tus conclusiones y análisis..."></textarea>
                    <button id="add-analysis-btn" class="mt-4 w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-300">
                        Agregar Análisis al Informe
                    </button>
                </div>
                
                <div class="bg-white p-6 rounded-xl shadow-md space-y-4">
                     <h2 class="text-xl font-semibold mb-2 border-b pb-2">Acciones</h2>
                     <button id="save-report-btn" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-300">Guardar Informe Actual</button>
                     <button id="download-pdf" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-300">Descargar Informe en PDF</button>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-md">
                    <h2 class="text-xl font-semibold mb-4 border-b pb-2">Informes Guardados</h2>
                    <div id="saved-reports-container" class="space-y-3 max-h-64 overflow-y-auto">
                        <p id="no-saved-reports" class="text-gray-500 text-center">No hay informes guardados.</p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div id="report-content" class="bg-white p-6 rounded-xl shadow-md">
                    <header class="text-center mb-8">
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Hospital Universitario Hernando Moncaleano Perdomo</h1>
                        <p class="text-gray-600 mt-2">Informe de Indicadores de Personal</p>
                    </header>
                    
                    <div id="analysis-display-container" class="hidden mb-8 p-4 bg-gray-50 rounded-lg border">
                        <h2 class="text-xl font-semibold mb-2 border-b pb-2">Análisis del Informe</h2>
                        <p id="analysis-display-text" class="text-gray-700 whitespace-pre-wrap break-words"></p>
                    </div>

                    <div class="space-y-12">
                        <div id="tipos-chart-container" class="hidden">
                            <h2 class="text-xl font-semibold mb-4 text-center">Distribución General por Tipo</h2>
                            <div class="relative h-72"><canvas id="tipos-chart"></canvas></div>
                        </div>
                        <div id="estudiantes-chart-container" class="hidden">
                            <h2 class="text-xl font-semibold mb-4 text-center">Estudiantes por Universidad</h2>
                            <div class="relative h-72 flex items-center justify-center"><canvas id="estudiantes-chart"></canvas></div>
                        </div>
                         <div id="residentes-chart-container" class="hidden">
                            <h2 class="text-xl font-semibold mb-4 text-center">Residentes por Universidad</h2>
                            <div class="relative h-72 flex items-center justify-center"><canvas id="residentes-chart"></canvas></div>
                        </div>
                         <div id="externos-chart-container" class="hidden">
                            <h2 class="text-xl font-semibold mb-4 text-center">Externos por Área y Servicio</h2>
                            <div class="relative h-72 flex items-center justify-center"><canvas id="externos-chart"></canvas></div>
                        </div>
                         <div id="auditores-chart-container" class="hidden">
                            <h2 class="text-xl font-semibold mb-4 text-center">Auditores Externos por EPS</h2>
                            <div class="relative h-72 flex items-center justify-center"><canvas id="auditores-chart"></canvas></div>
                        </div>
                    </div>
                    <div class="mt-12">
                        <h2 class="text-xl font-semibold mb-4">Resumen del Personal</h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detalles</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="personal-table-body" class="bg-white divide-y divide-gray-200"></tbody>
                            </table>
                             <p id="no-data" class="text-center text-gray-500 py-4">Aún no se han registrado datos.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <script src="script.js" defer></script>
</body>
</html>
//Lógica de inicio de sesión integrada directamente para asegurar el funcionamiento
        document.addEventListener('DOMContentLoaded', () => {
            const loginForm = document.getElementById('login-form');
            const errorMessage = document.getElementById('error-message');

            loginForm.addEventListener('submit', (e) => {
                e.preventDefault();

                const username = document.getElementById('username').value;
                const password = document.getElementById('password').value;

                const validUsername = 'admin';
                const validPassword = '1234';

                if (username === validUsername && password === validPassword) {
                    // Redirige al portal. Esta ruta es la más compatible.
                    window.location.href = 'portal.html';
                } else {
                    errorMessage.classList.remove('hidden');
                }
            });
        });
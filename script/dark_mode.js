const themeToggle = document.getElementById('theme-toggle');
const body = document.body;
const darkModeForm = document.getElementById('darkModeForm');
const darkModeInput = document.getElementById('darkModeInput');

themeToggle.addEventListener('click', () => {
    body.classList.toggle('dark-mode');
    const isDarkMode = body.classList.contains('dark-mode');
    darkModeInput.value = isDarkMode ? 1 : 0;
    darkModeForm.submit();
});
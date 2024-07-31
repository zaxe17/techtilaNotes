document.addEventListener('DOMContentLoaded', function() {
    Notification.requestPermission().then(perm => {
        if (perm === 'granted') {
            // Check for login messages
            let loginMessage = localStorage.getItem('loginMessage');
            let loginError = localStorage.getItem('loginError');

            if (loginMessage) {
                const notification = new Notification("Techtilanotes", {
                    body: loginMessage,
                    icon: "./assets/ibon.png"
                });

                notification.addEventListener("error", e => {
                    alert("Notification error");
                });

                // Clear the login message from localStorage
                localStorage.removeItem('loginMessage');
            }

            if (loginError) {
                const notification = new Notification("Techtilanotes", {
                    body: loginError,
                    icon: "./assets/ibon.png"
                });

                notification.addEventListener("error", e => {
                    alert("Notification error");
                });

                // Clear the login error from localStorage
                localStorage.removeItem('loginError');
            }
        }
    });
});

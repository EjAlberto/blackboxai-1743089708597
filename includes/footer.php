</main>
        </div>
    </div>

    <!-- Common JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Function to confirm deletions
        function confirmDelete() {
            return confirm('Are you sure you want to delete this record?');
        }

        // Function to show toast notifications
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 px-6 py-3 rounded-md shadow-md text-white ${
                type === 'success' ? 'bg-green-500' : 'bg-red-500'
            }`;
            toast.textContent = message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }

        // Check for URL success/error parameters
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('success')) {
                showToast(urlParams.get('success'));
            }
            if (urlParams.has('error')) {
                showToast(urlParams.get('error'), 'error');
            }
        });
    </script>
</body>
</html>
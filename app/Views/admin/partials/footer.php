    <script>
        // Initialize Lucide icons
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
        
        // Re-initialize icons after HTMX swaps
        document.body.addEventListener('htmx:afterSwap', () => {
            lucide.createIcons();
        });
    </script>
</body>
</html>

    </div> <!-- .main-container -->
    <footer>
        <p>© <?php echo date("Y"); ?> Sistem Inventaris Laboratorium</p>
    </footer>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Menggunakan $base_path untuk path JS -->
    <script src="<?php echo isset($base_path) ? htmlspecialchars($base_path) : ''; ?>js/script.js"></script>
    <!-- JavaScript spesifik halaman lain bisa ditaruh di sini jika perlu -->
</body>
</html>
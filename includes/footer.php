    </div> <!-- .main-container -->
    <footer>
        <p>© <?php echo date("Y"); ?> Sistem Inventaris Laboratorium</p>
    </footer>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> <!-- jQuery versi terbaru jika memungkinkan -->
    <!-- Path ke script.js relatif dari lokasi file yang meng-include footer.php -->
    <script src="<?php echo isset($base_path) ? htmlspecialchars($base_path) : ''; ?>js/script.js"></script>
</body>
</html>
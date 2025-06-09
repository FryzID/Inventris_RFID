<?php
// File: INVENTRIS_RFID/includes/footer.php
?>
    </div> <!-- .main-container -->
    <footer>
        <p>© <?php echo date("Y"); ?> Sistem Inventaris Laboratorium</p>
    </footer>

    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> 
    <!-- CoreUI JavaScript Bundle (Contoh, path mungkin berbeda) -->
    <script src="/coreui/js/coreui.bundle.min.js"></script> 
    <!-- Skrip Kustom Anda -->
    <script src="<?php echo isset($base_path) ? htmlspecialchars($base_path) : ''; ?>js/script.js"></script>
</body>
</html>
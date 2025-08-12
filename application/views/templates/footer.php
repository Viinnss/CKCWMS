</main><!-- End #main -->

<!-- ======= Footer ======= -->
<footer id="footer" class="footer">
	<div class="copyright">
		&copy; Copyright <strong><span>Cahaya Karomah Cemerlang</span></strong>. All Rights Reserved
	</div>
</footer><!-- End Footer -->

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Vendor JS Files -->
<script src="<?= base_url('assets'); ?>/vendor/sweet-alert/sweet-alert.js"></script>
<script src="<?= base_url('assets'); ?>/vendor/apexcharts/apexcharts.min.js"></script>
<script src="<?= base_url('assets'); ?>/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('assets'); ?>/vendor/chart.js/chart.umd.js"></script>
<script src="<?= base_url('assets'); ?>/vendor/echarts/echarts.min.js"></script>
<script src="<?= base_url('assets'); ?>/vendor/quill/quill.js"></script>
<script src="<?= base_url('assets'); ?>/vendor/simple-datatables/simple-datatables.js"></script>
<script src="<?= base_url('assets'); ?>/vendor/tinymce/tinymce.min.js"></script>
<script src="<?= base_url('assets'); ?>/vendor/php-email-form/validate.js"></script>
<script src="<?= base_url('assets'); ?>/vendor/select2/select2.js"></script>
<!-- Template Main JS File -->
<script src="<?= base_url('assets'); ?>/js/main.js"></script>
<script>
	$(document).ready(function() {
		$(".search-bar-toggle").click(function() {
			$("body").toggleClass("dark-mode");
			var currentIconClass = $(this).find("i").attr("class");
			if (currentIconClass === "bi bi-moon-stars-fill") {
				$(this).find("i").removeClass("bi-moon-stars-fill").addClass("bi-sun");
			} else {
				$(this).find("i").removeClass("bi-sun").addClass("bi-moon-stars-fill");
			}
		});
	});

	window.addEventListener('load', function () {
    const sidebar = document.getElementById('sidebar');
    if (!sidebar) return;
	
	// Restore scroll position from localStorage
    const savedScroll = localStorage.getItem('sidebarScroll');
    if (savedScroll !== null) {
        sidebar.scrollTop = parseInt(savedScroll, 10);
    }

    // Reset scroll position after a short delay to ensure the sidebar is fully rendered
    setTimeout(() => {
        if (savedScroll !== null) {
            sidebar.scrollTop = parseInt(savedScroll, 10);
        }
    }, 300);

    // Save scroll position on scroll
    sidebar.addEventListener('scroll', function () {
        localStorage.setItem('sidebarScroll', sidebar.scrollTop);
    });
});
</script>
</body>

</html>

</div><!-- /.col -->
    </div><!-- /.row -->
</div><!-- /.container-fluid -->

<script src="/Assets/js/jquery-1.11.1.min.js"></script>
<script src="/Assets/js/bootstrap.min.js"></script>
<script src="/Assets/js/chart.min.js"></script>
<script src="/Assets/js/chart-data.js"></script>
<script src="/Assets/js/easypiechart.js"></script>
<script src="/Assets/js/easypiechart-data.js"></script>
<script src="/Assets/js/bootstrap-datepicker.js"></script>
<script src="/Assets/js/bootstrap-table.js"></script>
<script src="/Assets/js/sweetalert2.min.js"></script>
<script>
    !function ($) {
        $(document).on("click","ul.nav li.parent > a > span.icon", function(){         
            $(this).find('em:first').toggleClass("glyphicon-minus");      
        }); 
        $(".sidebar span.icon").find('em:first').addClass("glyphicon-plus");
    }(window.jQuery);

    $(window).on('resize', function () {
      if ($(window).width() > 768) $('#sidebar-collapse').collapse('show')
    })
    $(window).on('resize', function () {
      if ($(window).width() <= 767) $('#sidebar-collapse').collapse('hide')
    })
</script>

<?php if (session()->getFlashdata('success')) : ?>
<script>
    $(document).ready(function () {
        swal("Success!", "<?= session()->getFlashdata('success'); ?>", "success");
    });
</script>
<?php endif; ?>

<?php if (session()->getFlashdata('error')) : ?>
<script>
    $(document).ready(function () {
        swal("Sorry!", "<?= session()->getFlashdata('error'); ?>", "error");
    });
</script>
<?php endif; ?>

<?php if (session()->getFlashdata('warning')) : ?>
<script>
    $(document).ready(function () {
        swal("Warning!", "<?= session()->getFlashdata('warning'); ?>", "warning");
    });
</script>
<?php endif; ?>

<?php if (session()->getFlashdata('info')) : ?>
<script>
    $(document).ready(function () {
        swal("Info!", "<?= session()->getFlashdata('info'); ?>", "info");
    });
</script>
<?php endif; ?>
</body>
</html>
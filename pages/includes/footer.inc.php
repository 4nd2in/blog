<footer><!-- Start Footer -->
    <div class="horizontal-line"></div>
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="footer-copyright text-center py-3">
                    <span id="year"></span> COPYRIGHT <i class="far fa-copyright"></i> | 4ND2IN | ALL RIGHTS RESERVED<br />
                </div>
            </div>
        </div>
    </div>
    <div class="horizontal-line"></div>
</footer><!-- End Footer -->

<script>
$('#year').text(new Date().getFullYear());
</script>

<!-- Sweet Alert -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<?php
// SHOWING ERRORS
if (isset($_GET['alert'])){
    $alertStatus = $_GET['alert'];
    echo showAlert($alertStatus);
}
if ($con){$con->close();}
?>

<!-- body tag must be beneath -->

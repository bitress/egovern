<?php
include_once 'config/init.php';

if (!$login->isLoggedIn()) {
    header("Location: login.php");
    die();
}

$db = Database::getInstance();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $resident_id = $_POST['resident_name'];
    $document_type = $_POST['certification_type'];
    $requester = isset($_POST['requester_name']) ? $_POST['requester_name'] : null;
    $clearance_purpose = isset($_POST['clearance_purpose']) ? $_POST['clearance_purpose'] : null;
    $community_tax_cert_number = isset($_POST['community_tax_cert_number']) ? $_POST['community_tax_cert_number'] : null;
    $community_tax_cert_date = !empty($_POST['community_tax_cert_date']) ? $_POST['community_tax_cert_date'] : null;

    $date_requested = date('Y-m-d H:i:s');
  

    try {

    $query = "INSERT INTO document_requests 
                    (resident_id, document_type, requester, clearance_purpose, community_tax_cert_number, community_tax_cert_date, date_requested) 
                  VALUES 
                    (:resident_id, :document_type, :requester, :clearance_purpose, :community_tax_cert_number, :community_tax_cert_date, :date_requested)";

        $stmt = $db->prepare($query);

        $stmt->bindParam(':resident_id', $resident_id);
        $stmt->bindParam(':document_type', $document_type);
        $stmt->bindParam(':requester', $requester);
        $stmt->bindParam(':clearance_purpose', $clearance_purpose);
        $stmt->bindParam(':community_tax_cert_number', $community_tax_cert_number);
        $stmt->bindParam(':community_tax_cert_date', $community_tax_cert_date);
        $stmt->bindParam(':date_requested', $date_requested);

        // Execute the statement
        $stmt->execute();

        echo "Record successfully inserted!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Dashboard - eGovern: EXPLORATORY DATA VISUALIZATION FOR ADVANCING COMMUNITY GOVERNANCE IN BARANGAY CULIONG,
        SALCEDO,</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.png" />
    <script data-search-pseudo-elements="" defer=""
        src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/js/all.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js" crossorigin="anonymous">
    </script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>

<body class="nav-fixed">
    <?php include 'includes/topbar.php'; ?>

    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <?php include 'includes/navbar.php'; ?>
        </div>

        <div id="layoutSidenav_content">
            <main>
                <header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
                    <div class="container-fluid px-4">
                        <div class="page-header-content">
                            <div class="row align-items-center justify-content-between pt-3">
                                <div class="col-auto mb-3">
                                    <h1 class="page-header-title">
                                        <div class="page-header-icon"><i
                                                class="fa-light fa-monitor-waveform text-light "
                                                data-feather="  book-open"></i></div>
                                        Request Document
                                    </h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <div class="container-xl px-4">
                    <div class="card">
                        <div class="card-body">
                            <form method="post" action="request-document.php">
                                <div class="mb-3">
                                    <label for="documentType" class="form-label">Document</label>
                                    <select class="form-control" name="certification_type" id="certification_type"
                                        onchange="toggleFields()">
                                        <option selected disabled>Select document</option>
                                        <?php
                                        $rd = new RequestDocument();
                                        $res = $rd->getDocuments();
                                        foreach ($res as $row): ?>
                                        <option value="<?= $row['document_id'] ?>"><?= $row['document_name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-3" id="resident_name_div">
                                    <label>Resident Name</label>
                                    <select class="form-control" name="resident_name" id="resident_name"
                                        style="width: 100%;">
                                        <option selected disabled>Select Resident</option>
                                    </select>
                                </div>

                                <div class="mb-3" id="requester_name_div">
                                    <label>Who is requesting the certification?</label>
                                    <input type="text" name="requester_name" id="requester_name" class="form-control" />
                                </div>

                                <div class="mb-3" id="clearance_purpose_div">
                                    <label>What is the purpose of the clearance?</label>
                                    <input type="text" name="clearance_purpose" id="clearance_purpose"
                                        class="form-control" />
                                </div>

                                <div class="mb-3" id="community_tax_cert_div">
                                    <label>Community Tax Certificate Number</label>
                                    <input type="text" name="community_tax_cert_number" id="community_tax_cert_number"
                                        class="form-control" />
                                </div>

                                <div class="mb-3" id="community_tax_date_div">
                                    <label>When was the Community Tax Certificate Number issued?</label>
                                    <input type="date" name="community_tax_cert_date" id="community_tax_cert_date"
                                        class="form-control" />
                                </div>

                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </main>

            <footer class="footer-admin mt-auto footer-light">
                <div class="container-xl px-4">
                    <div class="row">
                        <div class="col-md-6 small">Copyright © Your Website 2021</div>
                        <div class="col-md-6 text-md-end small">
                            <a href="#!">Privacy Policy</a>
                            ·
                            <a href="#!">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
    </script>
    <script src="js/scripts.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"
        integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"
        crossorigin="anonymous"></script>
    <script src="js/datatables/datatables-simple-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/litepicker/dist/bundle.js" crossorigin="anonymous"></script>
    <script src="js/litepicker.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
    $(document).ready(function() {
        $('#resident_name').select2({
            placeholder: 'Select Resident',
            allowClear: true,
            ajax: {
                url: 'sendData',
                type: 'POST',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        action: 'fetchResidentsName',
                        search: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;

                    return {
                        results: data.results,
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                },
                cache: true
            },
            minimumInputLength: 1
        });
    });


    document.getElementById('resident_name_div').style.display = 'none';
    document.getElementById('requester_name_div').style.display = 'none';
    document.getElementById('clearance_purpose_div').style.display = 'none';
    document.getElementById('community_tax_cert_div').style.display = 'none';
    document.getElementById('community_tax_date_div').style.display = 'none';

    function toggleFields() {
        var selectedDoc = document.getElementById('certification_type').value;

        document.getElementById('resident_name_div').style.display = 'none';
        document.getElementById('requester_name_div').style.display = 'none';
        document.getElementById('clearance_purpose_div').style.display = 'none';
        document.getElementById('community_tax_cert_div').style.display = 'none';
        document.getElementById('community_tax_date_div').style.display = 'none';


        if (selectedDoc == '3') {
            document.getElementById('resident_name_div').style.display = 'block';
        } else if (selectedDoc == '4') {
            document.getElementById('resident_name_div').style.display = 'block';
            document.getElementById('requester_name_div').style.display = 'block';
        } else if (selectedDoc == '1') {
            document.getElementById('resident_name_div').style.display = 'block';
            document.getElementById('clearance_purpose_div').style.display = 'block';
            document.getElementById('community_tax_cert_div').style.display = 'block';
            document.getElementById('community_tax_date_div').style.display = 'block';
        }
    }
    </script>
</body>

</html>
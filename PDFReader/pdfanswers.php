<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <script src="../includes/jscript/jquery-1.11.2.min.js"></script>
</head>
<body>
<?php
$page_title = "Users";

require_once("../includes/db_functions.php");
require_once("../database/PdfDB.php");

/*
 * Following code will list all the products
 */

// array for JSON response
$response = array();
$messages = array();
// include db connect class
//require_once __DIR__ . 'includes/db_functions.php';

// connecting to db
$db = get_db_connection();
$id = $_GET["id"];
// get all products from products table
//$result = AbsenceDB::getCurrentAbsences();
$SqlQuery = PdfDB::getIDAll($id);
//print_r(get_db_connection ());
$result = PdfDB::getIDAll($id);
// check for empty result
if (mysqli_num_rows($result) > 0) {
    // looping through all results
    // products node
    $response["pdfs"] = array();

    while ($row = mysqli_fetch_array($result)) {
        // temp user array

        $messages["ID"] = $row["ID"];
        //echo $messages["ID"]."<br/>";
        $messages["VragenPDF"] = $row["VragenPDF"];
        //echo $messages["title"]."<br/>";
        $messages["AntwoordenPDF"] = $row["AntwoordenPDF"];
        $messages["Offset"] = $row["Offset"];
        //echo $messages["message"]."<br/>";
        //echo $messages["visibility"]."<br/>";
        array_push($response["pdfs"], $messages);
    }
    // success
    $response["success"] = 1;

    // echoing JSON response
    //echo json_encode($response);
} else {
    // no products found
    $response["success"] = 0;
    $response["message"] = "No products found";

    // echo no users JSON
    //echo json_encode($response);
}
?>
<div>
    <button id="prev">Previous</button>
    <button id="next">Next</button>
    &nbsp; &nbsp;
    <span>Page: <span id="page_num"></span> / <span id="page_count"></span></span>

</div>

<div>
    <!--<canvas id="theCanvas" style="border:1px solid black"></canvas>-->
    <canvas id="the-canvas" style="border:1px solid black"></canvas>
</div>

<div>
    <button id="select">
        back
    </button>

</div>

<!-- for legacy browsers add compatibility.js -->
<!--<script src="../compatibility.js"></script>-->

<script src="../lib/pdfjs-1.3.91-dist%20(1)/build/pdf.js"></script>

<script id="script">

    var param = document.URL.split('#') ;
    /*var loadingTask =  PDFJS.getDocument('Answer Star book.pdf');
     loadingTask.promise.then(function (pdfDocument) {
     // Request a first page
     return pdfDocument.getPage(parseInt(param[1])).then(function (pdfPage) {
     // Display page on the existing canvas with 100% scale.
     var viewport = pdfPage.getViewport(1.0);
     var canvas = document.getElementById('theCanvas');
     canvas.width = viewport.width;
     canvas.height = viewport.height;
     var ctx = canvas.getContext('2d');
     var renderTask = pdfPage.render({
     canvasContext: ctx,
     viewport: viewport
     });
     return renderTask.promise;
     });
     }).catch(function (reason) {
     console.error('Error: ' + reason + ' ' + parseInt(param[1]));
     });*/

    //
    // If absolute URL from the remote server is provided, configure the CORS
    // header on that server.
    //
    var url = '<?php echo $messages["AntwoordenPDF"] ; ?>';


    //
    // Disable workers to avoid yet another cross-origin issue (workers need
    // the URL of the script to be loaded, and dynamically loading a cross-origin
    // script does not work).
    //
    // PDFJS.disableWorker = true;

    //
    // In cases when the pdf.worker.js is located at the different folder than the
    // pdf.js's one, or the pdf.js is executed via eval(), the workerSrc property
    // shall be specified.
    //
    // PDFJS.workerSrc = '../../build/pdf.worker.js';

    var pdfDoc = null;
    var
        pageNum;
    //var param = document.URL.split('#')[1];
    if(param != null || param != ""){
        pageNum = parseInt(param[1]);
    }else {
        pageNum = 1;
    }


    var pageRendering = false,
        pageNumPending = null,
        scale = 0.8,
        canvas = document.getElementById('the-canvas'),
        ctx = canvas.getContext('2d');
    // Get # parameter

    /**
     * Get page info from document, resize canvas accordingly, and render page.
     * @param num Page number.
     */
    function renderPage(num) {
        pageRendering = true;

        // Using promise to fetch the page
        pdfDoc.getPage(num).then(function(page) {
            var viewport = page.getViewport(scale);
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            // Render PDF page into canvas context
            var renderContext = {
                canvasContext: ctx,
                viewport: viewport
            };
            var renderTask = page.render(renderContext);

            // Wait for rendering to finish
            renderTask.promise.then(function () {
                pageRendering = false;
                if (pageNumPending !== null) {
                    // New page rendering is pending
                    renderPage(pageNumPending);
                    pageNumPending = null;
                }
            });
        });

        // Update page counters
        document.getElementById('page_num').textContent = pageNum;
        document.getElementById('select').onclick = function(){
            param[1]-= <?php echo $messages["Offset"];?>;
            window.location.href = "http://localhost:63342/PDFReader/pdfquestions.php?id=<?php echo $id;?>#"+param[1]; // window.location = window.location + "/currentpath/additional/params/here"
        }
    }

    /**
     * If another page rendering in progress, waits until the rendering is
     * finised. Otherwise, executes rendering immediately.
     */
    function queueRenderPage(num) {
        if (pageRendering) {
            pageNumPending = num;
        } else {
            renderPage(num);
        }
    }

    /**
     * Displays previous page.
     */
    function onPrevPage() {
        if (pageNum <= 1) {
            return;
        }
        pageNum--;
        queueRenderPage(pageNum);
    }
    document.getElementById('prev').addEventListener('click', onNextPage);

    /**
     * Displays next page.
     */
    function onNextPage() {
        if (pageNum >= pdfDoc.numPages) {
            return;
        }
        pageNum++;
        queueRenderPage(pageNum);
    }
    document.getElementById('next').addEventListener('click', onNextPage);

    /**
     * Asynchronously downloads PDF.
     */
    PDFJS.getDocument(url).then(function (pdfDoc_) {
            pdfDoc = pdfDoc_;
            document.getElementById('page_count').textContent = pdfDoc.numPages;


            // Initial/first page rendering
            renderPage(pageNum);
        }
    );

</script>







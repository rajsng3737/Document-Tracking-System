<?php

        echo '<div class="modal fade" id="receivedModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" style = "display:none">
        <div class="modal-dialog modal-dialog-centered" role="document">
           <div class="modal-content">
               <div class="modal-header border-0">
                   <h5 class="modal-title" id="exampleModalLongTitle">'.$modal_header.'</h5>
               </div>
               <div class = "p-3">
                    <div class="modal-body alert alert-'.$alert.'">
                        '.$modal_val.'
                    </div>
                </div>
               <div class="modal-footer border-0">
                   <button type="button" id = "close_modal" class="btn btn-secondary" data-dismiss="modal">Close</button>
               </div>
           </div>
       </div>
    </div>
    <script>
    $(document).ready(function() {
       $("#receivedModal").modal("show");
       $("#close_modal").click(function(){
           $("#receivedModal").modal("hide");
           location.replace("home.php");
       });
    });
    </script>';

?>
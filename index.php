<?php
/**
 * Created by Catur Pamungkas
 * Date: 01/11/2020.
 */
include 'header.php';
?> 
<body>
    <div class="container" id="start">
        <h1>MainGames Dadu</h1>
        <form action="" method="POST">
            <div class="form-group">
                <label for="player_num">Jumlah Pemain :</label>
                <input type="number" class="form-control" id="player_num" placeholder="Jumlah Pemain" name="player_num" value="<?php if (isset($_POST['player_num'])) {
    echo $_POST['player_num'];
} ?>">
            </div>
            <div class="form-group">
                <label for="dice_num">Jumlah Dadu :</label>
                <input type="number" class="form-control" id="dice_num" placeholder="Jumlah Dadu" name="dice_num" value="<?php  if (isset($_POST['dice_num'])) {
    echo $_POST['dice_num'];
}    ?>">
            </div>
            <button type="submit" class="btn btn-primary">Mulai Permainan</button>
        </form>
    </div>

    <script type="text/javascript">
        $(document).ready(function(){
            $('#play').on('submit', function(event){
                event.preventDefault();
                $("#button").hide();
                var player_num = $("#player_num").val();
                var dice_num = $("#dice_num").val();

                $.ajax({
                type: "POST",
                url: "play.php",
                data: $(this).serialize(),
                success: function(data) {
                    $('#tbody').append(data);
                }
                });
                return false;
            });
        });
    </script>
    <?php
    if (!empty($_POST['player_num']) && ($_POST['player_num'] > 1) && !empty($_POST['dice_num']) && ($_POST['dice_num'] >= 1)) {
        ?>
        <form method="POST" id="play">
        <table class="mt-5 table-fill">
            <thead>
                <tr>
                    <th class="text-center"></th>
                    <?php for ($i = 1; $i <= $_POST['player_num']; ++$i) { ?>
                        <th class="text-center">Pemain #<?php echo $i; ?></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody class="table-hover" id="tbody">
                <tr id="button">
                    <td colspan="<?php echo $_POST['player_num'] + 1; ?>" class="text-center">
                        <input type="hidden" id="player_num" name="player_num" value="<?php echo $_POST['player_num']; ?>">
                        <input type="hidden" id="dice_num" name="dice_num" value="<?php echo $_POST['dice_num']; ?>">
                        <button class="btn btn-primary pull-right" name="submit" id="submit" value="Lempar Dadu!">
                            Lempar Dadu!
                        </button>
                        <div id="roll"></div>
                    </td>
                </tr>
            </tbody>
        </table>
        </form>
    <?php
    } else {
        echo '<center><h2>Catatan : Minimal Jumlah Pemain <b>2</b> & Jumlah Dadu <b>1</b>!</h2></center>';
    }
    ?>


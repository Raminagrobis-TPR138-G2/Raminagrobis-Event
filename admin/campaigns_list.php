<?php
$title = "Liste des campagnes";
include_once "../src/layout/headerAdmin.php";
include_once "../src/config.php";
include_once "../src/actions/database-connection.php";
include_once "../src/actions/function.php";

$modalPrint = false;
if (isset($_SESSION["error_campaign"])) {
    if ($_SESSION["error_campaign"]) { ?>
        <div class="alert alert-danger">
            <?= $_SESSION["status_campaign"] ?>
        </div>
    <?php } else {
        $modalPrint = true;
        //création d'une popup pour afficher le lien du formulaire qui vient d'être créer
        modalBodyLink("modalCampaignSuccess",$_SESSION["status_campaign"],"success",$_SESSION["title_campaign"],$_SESSION["start_campaign"],$_SESSION["end_campaign"],$_SESSION["id_campaign"]);

        unset($_SESSION["id_campaign"]);
        unset($_SESSION["start_campaign"]);
        unset($_SESSION["end_campaign"]);
        unset($_SESSION["title_campaign"]);
    }
    unset($_SESSION["error_campaign"]);
    unset($_SESSION["status_campaign"]);
}


$search = filter_input(INPUT_GET, 'search');
if (isset($search)) {
    $campaigns_list = sqlCommand("SELECT id,title,description,start_date,end_date,organisation FROM form WHERE title LIKE :search OR description LIKE :search OR organisation LIKE :search", [":search" => "%" . $search . "%"], $conn);
} else {
    $campaigns_list = sqlCommand("SELECT id,title,description,start_date,end_date,organisation FROM form", [], $conn);
}
$today = date("Y-m-d");

?>

<div class="container-xxl mt-5 mb-3">

    <?php searchData("Liste des campagnes", $search, "campaigns_list.php","campaigns_list.php") ?>



    <table class="table table-striped ">
        <thead>
        <tr class="text-center">
            <th scope="col">ID</th>
            <th scope="col">Nom</th>
            <th scope="col">Organisation</th>
            <th scope="col">Description</th>
            <th scope="col">Début</th>
            <th scope="col">Fin</th>
            <th scope="col">Status</th>
            <th scope="col">Action</th>

        </tr>
        </thead>
        <tbody>
        <?php if (count($campaigns_list) == 0) {
            ?>
            <tr>
                <th colspan="9" class="text-center">Aucune donnée</th>
            </tr>
            <?php
        } else {
            foreach ($campaigns_list as $data) {
                ?>
                <tr class="text-center">
                    <th scope="row"><?= dataDBSafe($data["id"]) ?></th>
                    <td class="table-list"><?= dataDBSafe($data["title"]) ?></td>
                    <td class="table-list"><?= dataDBSafe($data["organisation"]) ?></td>
                    <td class="table-list"><?= dataDBSafe($data["description"]) ?></td>
                    <td><?= dataDBSafe($data["start_date"]) ?></td>
                    <td><?= dataDBSafe($data["end_date"]) ?></td>
                    <td><?php if ($today >= $data['start_date'] and $today <= $data['end_date']) {
                            echo "<span class='text-success'>En cours</span>";
                        } else if ($today < $data['start_date']) {
                            echo "<span class='text-warning'>Commence dans " . nbDays($today, $data['start_date']) . " jours</span>";
                        } else {
                            echo "<span class='text-danger'>Terminé</span>";
                        } ?></td>
                    <td>

                        <a href="./campaign_data.php?id=<?= $data["id"] ?>" class="btn btn-success"><span
                                    class="fad fa-database"></span></a>
                        <a href="export.php?id=<?= $data["id"] ?>" class="btn btn-success"><span
                                    class="fad fa-download"></span></a>
                        <?php modalButton("<span class='far fa-link'></span>","outline-primary","modalLinkCampaign".$data["id"]); ?>
                        <!-- Bouton afficher lien du formulaire -->
                        <a href="./campaign.php?id=<?= $data["id"] ?>" class="btn btn-danger"><span
                                    class="far fa-edit"></span></a>

                    </td>
                </tr>
            <?php }
            foreach ($campaigns_list as $data) {
                modalBodyLink("modalLinkCampaign".$data["id"],"Lien du formulaire","light",$data["title"],$data["start_date"],$data["end_date"],$data["id"]);
            }
        } ?>
        </tbody>
    </table>
</div>
<script>

    <?php if ($modalPrint) { //afficher popup au chargement de la page
        echo "var myModal = new bootstrap . Modal(document . getElementById('modalCampaignSuccess'), {});
        myModal . show();";
    };?>
</script>
<?php
include_once "../src/layout/footer.php";
?>

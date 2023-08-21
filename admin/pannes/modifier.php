<?php
    require('../../config.php');
    if(!isset($_SESSION['role'])){
        header('Location: ../../login.php');
    }
	if($_SESSION['role']!='admin'){
        header('Location: ../../login.php');
    }
    if(isset($_GET['id'])){
        if(!filter_var($_GET['id'],FILTER_VALIDATE_INT)){
            header('Location: /gestiontransport/admin/camions/etat.php');
        }
        $id_panne=$_GET['id'];
        $sql = "SELECT * FROM panne WHERE id_panne=$id_panne";
        $panne_result = $conn->query($sql);
        if ($panne_result->num_rows == 0 ) {
            header('Location: /gestiontransport/admin/camions/etat.php');
        }
    }else{
        header('Location: /gestiontransport/admin/camions/etat.php');
    }
    if(isset($_POST['id_camion'],$_POST['datedebut'],$_POST['datefin'],$_POST['cout'])){
        $id_camion=$_POST['id_camion'];
        $datedebut=$_POST['datedebut'];
        $cout=$_POST['cout'];
        if($_POST['datefin']){
            $datefin=$_POST['datefin'];
            $stmt = $conn->prepare("UPDATE panne SET id_camion=?, datedebut_panne=?, datefin_panne=?, cout_panne=? WHERE id_panne=?");
            $stmt->bind_param("sssss", $id_camion,$datedebut,$datefin,$cout,$id_panne);
            $stmt->execute();
        }else{
            $stmt = $conn->prepare("UPDATE panne SET id_camion=?, datedebut_panne=?, cout_panne=? WHERE id_panne=?");
            $stmt->bind_param("ssss", $id_camion,$datedebut,$cout,$id_panne);
            $stmt->execute();
            $datefin=null;
        }
        header('Location: /gestiontransport/admin/camions/etat.php');
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<meta name="description" content=""/>
		<meta name="author" content=""/>
		<title><?php echo(customname('Modifier Panne #'.$_GET['id'])) ?></title>
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
		<link rel="stylesheet" href="../../assets/css/dashboard.css">
    </head>
    <body>
        <div class="d-flex" id="wrapper">
            <div class="border-end bg-white" id="sidebar-wrapper">
                <div class="sidebar-heading border-bottom bg-light"><?php echo APP_NAME; ?></div>
                <div class="list-group list-group-flush">
					<a class="list-group-item list-group-item-action list-group-item-light p-3" href="/gestiontransport/admin/" >Tableau de bord</a>
                    <a class="list-group-item list-group-item-action list-group-item-light p-3" href="/gestiontransport/admin/commandes.php">Commandes</a>
                    <a class="list-group-item list-group-item-action list-group-item-light p-3" href="/gestiontransport/admin/camions/">Gestion des véhicules</a>
                    <a class="list-group-item list-group-item-action list-group-item-primary p-3" href="/gestiontransport/admin/camions/etat.php">Etat des véhicules</a>
                    <a class="list-group-item list-group-item-action list-group-item-light p-3" href="/gestiontransport/admin/clients.php">Gestion des clients</a>
                </div>
            </div>
            <div id="page-content-wrapper">
                <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
                    <div class="container-fluid">
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $_SESSION['nom_admin'].' '.$_SESSION['prenom_admin']; ?></a>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="/gestiontransport/admin/commandes.php">Commandes</a>
                                        <a class="dropdown-item" href="/gestiontransport/admin/camions/etat.php">Etat des véhicules</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="../../logout.php">Déconnexion</a>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
                <div class="container-fluid">
                    <h1 class="mt-4">Modifier les informations de la panne #<?php echo $_GET['id']; ?></h1>
					<div class="row mb-4">
                        <div class="col-xl-6 p-5">
                            <?php while ($panne=$panne_result->fetch_assoc()){    ?>
                            <form id="save" method="POST">
                                <div class="form-floating mb-3">
                                    <input class="form-control" id="id_camion" name="id_camion" type="number" placeholder="Ex: 1" value="<?php echo $panne["id_camion"]?>" required/>
                                    <label for="id_camion">Numéro du véhicule</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input class="form-control" id="datedebut" name="datedebut" type="datetime-local" min="<?=date('Y-m-d\Th:i')?>" value="<?php echo $panne["datedebut_panne"]?>" required/>
                                    <label for="datedebut">Date de début</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input class="form-control" id="datefin" name="datefin" type="datetime-local" min="<?=date('Y-m-d\Th:i')?>" value="<?php echo $panne["datefin_panne"]?>" />
                                    <label for="datefin">Date de fin (laisser vide si inestimé)</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input class="form-control" id="cout" name="cout" type="number" min="500" placeholder="Ex: 500000"  value="<?php echo $panne["cout_panne"]?>" required/>
                                    <label for="cout">Coût de la panne en FCFA</label>
                                </div>
                                <div class="d-grid"><button class="btn btn-primary btn-lg" type="submit">Enregistrer</button></div>
                            </form>
                            <?php } ?>
                        </div>
                    </div>
				</div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.7.0.min.js" crossorigin="anonymous"></script>
    </body>
</html>
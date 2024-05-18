<?php
class controleur
{
    public function accueil()
    {
        $lesAnnonces = array();
        $message = null;
        if (isset($_POST['cp']) && $_POST['cp1'] != null) {
            $cp = htmlspecialchars($_POST['cp1']);
            $cpoupas = (new annonces)->filtreCP($cp);
            if ($cpoupas == false) {
                $message = "Il n'y a aucune annonce dans cette ville.";
                $lesAnnonces = null;
            } else {
                $lesAnnonces = $cpoupas;
            }
        } else {
            $lesAnnonces = (new annonces)->getAllAnnonces();
        }
        $lesPhotos = (new photo)->getAllPhoto();
        (new vue)->accueil($lesAnnonces, $lesPhotos, $message);
    }


    public function connexion()
    {
        if (isset($_POST["ok"])) {
            $mdp = $_POST['motdepasse'];
            $email = htmlspecialchars($_POST['email']);
            if ((new utilisateurs)->connexion($email, $mdp)) {
                $this->accueil();
            } else {
                $message = 'Mauvais login/mot de passe';
                (new vue)->connexion($message);
            }

        } else {
            (new vue)->connexion();
        }
    }

    public function inscription()
    {
        if (isset($_POST['ok'])) {
            $nom = htmlspecialchars($_POST['nom']);
            $prenom = htmlspecialchars($_POST['prenom']);
            $mail = $_POST['email'];
            $mdp = $_POST['motdepasse'];
            if (!empty($nom and $prenom and $mail and $mdp)) {
                if (!(new Utilisateurs)->existEmail($mail)) {
                    if ((new utilisateurs)->inscription($nom, $prenom, $mail, $mdp) == true) {
                        header("Location: index.php?action=connexion");
                    } else {
                        $message = "un probleme est survenu lors de l'inscription";
                        (new vue)->inscription($message);
                    }
                } else {
                    $message = "Cette adresse mail est déja attribué à un autre compte.";
                    (new vue)->inscription($message);
                }
            } else {
                $message = "Veuillez remplir tous les champs";
                (new vue)->inscription($message);
            }

        } else {
            (new vue)->inscription();
        }

    }

    public function annonces()
    {

        $id = $_GET['id'];
        $ID = $_GET['ID'];

        $uneAnnonce = array();
        $uneReservation = array();
        $lesPhotos = (new photo)->getPhoto($id);
        $uneAnnonce = (new annonces)->getUneAnnonce($ID);
        $uneReservation = (new periode)->getPeriode($id);

        if (isset($_POST['reserver'])) {
            $value = $_POST['selectionner'];

            $value = explode(', ', $value);
            $message = "Réservation effectué avec succès";
            $prix = (new periode)->getPrix($value[0]);

            (new reservation)->ajoutReservation($_SESSION['connexion'], $value[0], $id, $prix[0]["prix"], $value[1], $value[2]);
            (new vue)->uneAnnonce($uneAnnonce, $lesPhotos, $uneReservation, null, $message);
        } else {
            (new vue)->uneAnnonce($uneAnnonce, $lesPhotos, $uneReservation);
        }

    }

    public function afficherAppartement()
    {
        $id = 1;
        $lesLogements = array();
        $lesPhotos = (new photo)->getAllPhoto();
        $lesLogements = (new annonces)->getAnnonces($id);


        (new vue)->appartement($lesLogements, $lesPhotos);
    }

    public function afficherMaison()
    {
        $id = 2;
        $lesLogements = array();
        $lesPhotos = (new photo)->getAllPhoto();
        $lesLogements = (new annonces)->getAnnonces($id);
        (new vue)->maison($lesLogements, $lesPhotos);

    }

    public function profilProprio()
    {
        if ($_SESSION['role'] == 'proprio') {
            $utilisateurs = array();
            $annonces = array();
            $utilisateurs = (new utilisateurs)->getInfo($_SESSION['connexion']);
            $annonces = (new annonces)->getAnnonceProprio($_SESSION['connexion']);
            (new vue)->profilProprio($utilisateurs, null, null, null);


        } else {
            $utilisateurs = (new utilisateurs)->getInfo($_SESSION['connexion']);
            $reservation = (new reservation)->getReservationUser($_SESSION['connexion']);
            (new vue)->profilProprio($utilisateurs, null, null, $reservation);
        }


    }


    public function ajouterPeriode()
    {
        $annonces = array();
        $lesPhotos = (new photo)->getAllPhoto();
        $erreur = null;
        $succes = null;
        $annonces = (new annonces)->getAnnonceProprio($_SESSION['connexion']);


        foreach ($annonces as $annonce) {
            if (isset($_POST[$annonce['ID_Logements']])) {
                $dateDebut = htmlspecialchars($_POST['debutDate']);
                $finDate = htmlspecialchars($_POST['finDate']);
                $prix = htmlspecialchars($_POST['prix']);
                $id = $annonce['ID_Logements'];
                if (empty($prix) || empty($dateDebut) || empty($finDate)) {
                    $erreur = "Veuillez remplir tous les champs !";
                } else {
                    if ($dateDebut > $finDate) {
                        $erreur = "La date de début ne peut pas être supérieur a la date de fin";
                    } else {
                        if ((new periode)->ajouterPeriode($id, $dateDebut, $finDate, $prix)) {
                            $succes = "Periode de réservations bien ajoutée !";
                        } else {
                            $erreur = "Un probleme est survenu";
                        }
                    }
                }
            }
        }
        (new vue)->ajouterPeriode($annonces, $lesPhotos, $erreur, $succes);

    }

    public function supprimerPeriode()
    {
        $lesPhotos = (new photo)->getAllPhoto();
        $erreur = null;
        $succes = null;
        $annonces = (new annonces)->getAnnonceProprio($_SESSION['connexion']);
        $periode = array();

        foreach ($annonces as $annonce) {
            $periode[$annonce["ID_Logements"]] = (new periode)->getReservation($annonce["ID_Logements"]);

            // Vérifier si la période existe pour cette annonce
            if (empty($periode[$annonce["ID_Logements"]])) {
                continue;
            }

            // Le reste du code pour les annonces avec périodes existantes
            if (!empty($periode[$annonce["ID_Logements"]]) && isset($periode[$annonce["ID_Logements"]][0]["ID"])) {
                $id = $periode[$annonce["ID_Logements"]][0]["ID"];

                if (isset($_POST[$annonce["ID_Logements"]])) {
                    (new periode)->annulerPeriode($id);
                    $succes = "La période a bien été supprimée";
                } else {
                    $id = null;
                }
            }
        }

        (new vue)->supprimerPeriode($annonces, $lesPhotos, $periode, $erreur, $succes);
    }


    public function modifierPeriode()
    {
        $annonces = array();
        $lesPhotos = (new photo)->getAllPhoto();
        $erreur = null;
        $succes = null;
        $periodesReservations = array();
        $annonces = (new annonces)->getAnnonceProprio($_SESSION['connexion']);

        foreach ($annonces as $annonce) {
            $periodeReservation = (new periode)->getReservation($annonce['ID_Logements']);
            $periodesReservations[$annonce['ID_Logements']] = $periodeReservation;


            if (isset($_POST[$annonce['ID_Logements']])) {
                $dateDebut = htmlspecialchars($_POST['dateDebut']);
                $finDate = htmlspecialchars($_POST['dateFin']);
                $prix = htmlspecialchars($_POST['prix']);
                $id = $periodesReservations[$annonce['ID_Logements']][0]['ID'];
                if (empty($prix) || empty($dateDebut) || empty($finDate)) {
                    $erreur = "Veuillez remplir tous les champs !";
                } else {
                    if ($dateDebut > $finDate) {
                        $erreur = "La date de début ne peut pas être supérieur a la date de fin";
                    } else {

                        if ((new periode)->modifierReservation($id, $dateDebut, $finDate, $prix)) {
                            $succes = "Periode de réservations bien ajoutée !";
                        } else {
                            $erreur = "Un probleme est survenu";
                        }
                    }
                }

            }

        }
        (new vue)->afficherModifierPeriode($periodesReservations, $annonces, $lesPhotos, $erreur, $succes);

    }


    public function deconnexion()
    {
        (new utilisateurs)->deconnexion();
        header('Location: index.php?action=accueil');
    }



}


?>
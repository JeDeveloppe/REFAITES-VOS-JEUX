<?php
@session_start ();
include_once("../config.php");
$titreDeLaPage = "Nous soutenir | ".$GLOBALS['titreDePage'];
$descriptionPage = "Pour soutenir notre service, vous pouvez faire un don ou acheter nos jeux complets à petits prix.";
include_once("../bdd/connexion-bdd.php");
include_once("../commun/haut_de_page.php");
include_once("../commun/alertMessage.php");

?>
<div class="container-fluid mt-5">
    <h1 class="col-12 text-center mt-2">Nous soutenir</h1>
 
    <div class="row">
        <div class="col-11 col-lg-8 mx-auto">
        
            <p class="text-center my-3">
                Que vous soyez un particulier, un professionnel du monde du jeu ou du réemploi, ce projet a besoin de vous pour se pérenniser et se développer.
            </p> 
              
            <article class="my-4">
                <h4 class="text-info">Acheter des jeux :</h4>

                <p class="mt-2 text-justify">
                    Le service propose aux particuliers et aux professionnels du bassin caennais  d’acquérir des jeux complets d’occasion à petits prix ! 
                    En achetant des jeux sur le site vous contribuez à une démarche citoyenne de réduction des déchets !
                </p>
            </article>

            <article class="my-5">
                <h4 class="text-info">Donner des jeux :</h4>

                <ul>
                    <li>Le service récupère les jeux complets et incomplets ainsi que les pièces détachées (pions, dés, sabliers…).
                        <ol><span class="text-danger">Nous ne récupérons pas les puzzles ni les jouets.</span></ol>
                    </li>
                    <li class="pt-3">Si vous êtes sur Caen, vous pouvez les déposer à la Coop 5 pour 100 – 33 route de Trouville – Caen.</li>
                    <li class="pt-3">Si vous êtes d’une autre région vous pouvez les envoyer par voie postale ou colissimo à l’adresse suivante :
                        <ol>Antoine Gallée – 24 rue froide – 14980 ROTS.</ol>
                        <ol><span class="text-danger">Le service ne peut pas prendre en charge les frais de port.</span></ol>
                    </li>
                    <li class="pt-3">Vous pouvez aussi les expédier via Mondial Relay:
                        <ol> -> point relais CORA CAEN à Rots.</ol>
                        <ol> Email: contact@refaitesvosjeux.fr</ol>
                    </li>
                </ul>

                <p class="mt-2 text-justify">
                    Si vous habitez près de Rots vous pouvez aussi déposer vos jeux à mon domicile (24 rue froide).<br/>
                    En mon absence il est possible de les déposer devant la porte, elle est abritée.<br/>
                    Nous vous remercions par avance pour vos dons ! Nous nous ferons un plaisir de les ajouter aux catalogues !
                </p>
            </article>

            <article class="my-5">
                <h4 class="text-info">Agrandir le réseau:</h4>

                <p class="mt-2 text-justify">
                    Le service cherche à développer le réseau autour du réemploi du jouet: vendeurs de pièces détachées, de jeux complets ou points de collecte.<br/>
                    Vous souhaitez devenir l'un de nos <a class="text-info" href="/carte-des-partenaires/france/">partenaires</a> ? N'hésitez pas à nous contacter !
                </p>

                <div class="row mt-3 justify-content-center">
                    <div class="col-8 col-md-4 card mt-4 shadow">
                        <video controls autoplay loop>
                            <source src="/images/photos/animationFrance.mp4" type="video/mp4">
                        </video> 
                    </div>
                </div>
            </article>

   

            <p class="text-center my-5">
            Pour toute question merci d'utiliser le <a href="/contact/" class="text-info">formulaire en ligne</a>. 
            </p>

            <div class="col-12 mt-5 text-center">
                <figure class="figure col-11 col-sm-8 col-md-6">
                    <img src="/images/photos/donOuverture.JPG" class="figure-img img-fluid rounded" alt="photo exemple de dons">
                    <figcaption class="figure-caption text-center">Exemple de dons.</figcaption>
                </figure>
            </div>
    </div>
</div>
<?php
include_once("../commun/bas_de_page.php");
?>

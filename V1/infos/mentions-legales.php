<?php
@session_start ();
include_once("../config.php");
$titreDeLaPage = "Mentions légales  | ".$GLOBALS['titreDePage'];
$descriptionPage = "Les mentions légales concernant le site.";
include_once("../commun/haut_de_page.php");
include_once("../commun/alertMessage.php");
?>

<div class="container-fluid">
    <h1 class="col mt-4 text-center">Mentions légales</h1>

    <div class="col mt-4 mb-4">

        <p>Conformément aux dispositions des articles 6-III et 19 de la Loi n° 2004-575 du 21 juin 2004 pour la Confiance dans l’économie numérique et de la protection des données (RGPD) de l’Union européenne (UE), nous portons à la connaissance des utilisateurs et visiteurs du site : <?php echo $GLOBALS['siteUrl'];?> les informations suivantes :</p>

        <div class="col mt-4 ml-4">
            <h2>Informations légales</h2>
                <p>
                Propriétaire du site : <?php echo $GLOBALS['societe']; ?><br />
                Adresse de courrier électronique : <?php echo $GLOBALS['adresseMailSite']; ?><br />
                Adresse : <?php echo $GLOBALS['adresseSociete']; ?><br />
                Hébergeur du site: <?php echo $GLOBALS['hebergeur']; ?><br/>
                </p>
                <p>
                Créateur du site: <?php echo $GLOBALS['societe_creatrice_site']; ?><br />
                <i class="fas fa-pen"></i> Webmaster : <?php echo $GLOBALS['webmaster-responsableSite']; ?>
                </p>
        </div>
    </div>

    <div class="col mt-4">
    <h2 id="cgu" class="col text-center mb-3">Conditions générales d’utilisation du site et des services proposés</h2>

    L’utilisation du site <?php echo $GLOBALS['siteUrl']; ?> implique l’acceptation pleine et entière des conditions générales d’utilisation décrites ci-aprés. <i class="fas fa-server text-gray fa-2x"></i><br/>
    Ces conditions d’utilisation sont susceptibles d’être modifiées ou complétées à tout moment, sans préavis, aussi les utilisateurs du site <?php echo $GLOBALS['siteUrl']; ?> sont invités à les consulter de manière régulière. <?php echo $GLOBALS['siteUrl']; ?> est par principe accessible aux utilisateurs 24/24h, 7/7j, sauf interruption, programmée ou non, pour les besoins de sa maintenance ou cas de force majeure. En cas d’impossibilité d’accès au service, <?php echo $GLOBALS['siteUrl']; ?> s’engage à faire son maximum afin de rétablir l’accès au service et s’efforcera alors de communiquer préalablement aux utilisateurs les dates et heures de l’intervention. N’étant soumis qu’à une obligation de moyen, <?php echo $GLOBALS['siteUrl']; ?> ne saurait être tenu pour responsable de tout dommage, quelle qu’en soit la nature, résultant d’une indisponibilité du service.

    Le site <?php echo $GLOBALS['siteUrl']; ?> est mis à jour régulièrement par le proprietaire du site. De la même façon, les mentions légales peuvent être modifiées à tout moment, sans préavis et s’imposent à l’utilisateur sans réserve. L’utilisateur est réputé les accepter sans réserve et s’y référer régulièrement pour prendre connaissance des modifications.
    Le site <?php echo $GLOBALS['siteUrl']; ?> se réserve aussi le droit de céder, transférer, ce sans préavis les droits et/ou obligations des présentes CGU et mentions légales. En continuant à utiliser les services du site <?php echo $GLOBALS['siteUrl']; ?> , l’utilisateur reconnaît accepter les modifications des conditions générales qui seraient intervenues.
    </div>

    <div class="col mt-4">
    <h2>Description des services fournis</h2>

    Le site <?php echo $GLOBALS['siteUrl']; ?> a pour objet de fournir une information concernant l’ensemble des activités de la société.
    Le proprietaire du site s’efforce de fournir sur le site <?php echo $GLOBALS['siteUrl']; ?> des informations aussi précises que possible. Toutefois, il ne pourra être tenue responsable des omissions, des inexactitudes et des carences dans la mise à jour, qu’elles soient de son fait ou du fait des tiers partenaires qui lui fournissent ces informations.
    Tous les informations proposées sur le site <?php echo $GLOBALS['siteUrl']; ?> sont données à titre indicatif, sont non exhaustives, et sont susceptibles d’évoluer. Elles sont données sous réserve de modifications ayant été apportées depuis leur mise en ligne.
    </div>

    <div class="col mt-4">
    <h2>Limites de responsabilité</h2>

    Le site <?php echo $GLOBALS['siteUrl']; ?> utilise les technologies PHP <a href="https://www.php.net/manual/fr/intro-whatis.php" target="_blank"><i class="fab fa-php fa-2x text-info"></i></a> et Javascript <a href="https://developer.mozilla.org/fr/docs/Learn/JavaScript/First_steps/What_is_JavaScript" target="_blank"><i class="fab fa-js fa-2x text-warning"></i></a>.<br />
    Le site <?php echo $GLOBALS['siteUrl']; ?> ne saurait être tenu responsable des erreurs typographiques ou inexactitudes apparaissant sur le service, ou de quelque dommage subi résultant de son utilisation. L’utilisateur reste responsable de son équipement et de son utilisation, de même il supporte seul les coûts directs ou indirects suite à sa connexion à Internet.

    L’utilisateur du site <?php echo $GLOBALS['siteUrl']; ?> s’engage à accéder à celui-ci en utilisant un matériel récent, ne contenant pas de virus et avec un navigateur de dernière génération mise à jour.

    L’utilisateur dégage la responsabilité de <?php echo $GLOBALS['siteUrl']; ?> pour tout préjudice qu’il pourrait subir ou faire subir, directement ou indirectement, du fait des services proposés. Seule la responsabilité de l’utilisateur est engagée par l’utilisation du service proposé et celui-ci dégage expressément le site <?php echo $GLOBALS['siteUrl']; ?> de toute responsabilité vis à vis de tiers.
    Des espaces interactifs (possibilité de poser des questions dans l’espace contact) sont à la disposition des utilisateurs. Le site <?php echo $GLOBALS['siteUrl']; ?> se réserve le droit de supprimer, sans mise en demeure préalable, tout contenu déposé dans cet espace qui contreviendrait à la législation applicable en France, en particulier aux dispositions relatives à la protection des données. Le cas échéant, le proprietaire du site se réserve également la possibilité de mettre en cause la responsabilité civile et/ou pénale de l’utilisateur, notamment en cas de message à caractère raciste, injurieux, diffamant, ou pornographique, quel que soit le support utilisé (texte, photographie…).
    Il est ici rappelé que le développeur du site <?php echo $GLOBALS['siteUrl']; ?> garde trace de l’adresse mail, et de l’adresse IP de l’utilisateur. En conséquence, il doit être conscient qu’en cas d’injonction de l’autorité judiciaire il peut être retrouvé et poursuivi.
    </div>

    <div class="col mt-4">
    <h2>Propriété intellectuelle et contrefaçons</h2>

    Le proprietaire du site est propriétaire des droits de propriété intellectuelle ou détient les droits d’usage sur tous les éléments accessibles sur le site, notamment les textes, images, graphismes, logo, icônes, sons, logiciels…
    Toute reproduction, représentation, modification, publication, adaptation totale ou partielle des éléments du site, quel que soit le moyen ou le procédé utilisé, est interdite, sauf autorisation écrite préalable à l’email : <?php echo $GLOBALS['adresseMailSite']; ?>
    Toute exploitation non autorisée du site ou de l’un quelconque de ces éléments qu’il contient sera considérée comme constitutive d’une contrefaçon et poursuivie conformément aux dispositions des articles L.335-2 et suivants du Code de Propriété Intellectuelle.
    </div>

    <div class="col mt-4">
    <h2>Liens hypertextes et cookies <i class="fas fa-link text-info"></i> <i class="fas fa-cookie text-brun"></i></h2>

    Le site <?php echo $GLOBALS['siteUrl']; ?> contient un certain nombre de liens hypertextes vers d’autres sites (partenaires, informations …) mis en place avec l’autorisation du proprietaire du site . Cependant, le proprietaire du site n’a pas la possibilité de vérifier le contenu des sites ainsi visités et décline donc toute responsabilité de ce fait quand aux risques éventuels de contenus illicites.

    L’utilisateur est informé que lors de ses visites sur le site <?php echo $GLOBALS['siteUrl']; ?>, un ou des cookies sont susceptible de s’installer automatiquement sur son ordinateur. Un cookie est un fichier de petite taille, qui ne permet pas l’identification de l’utilisateur, mais qui enregistre des informations relatives à la navigation d’un ordinateur sur un site. Les données ainsi obtenues visent à faciliter la navigation ultérieure sur le site, et ont également vocation à permettre diverses mesures de fréquentation.

    Le paramétrage du logiciel de navigation permet d’informer de la présence de cookie et éventuellement, de refuser de la manière décrite à l’adresse suivante : www.cnil.fr Le refus d’installation d’un cookie peut entraîner l’impossibilité d’accéder à certains services. L’utilisateur peut toutefois configurer son ordinateur de la manière suivante, pour refuser l’installation des cookies :
    
        <ul class="list-unstyled ml-2 mt-2">
            <li><i class="fab fa-internet-explorer"></i> Sous Internet Explorer : onglet outil / options internet. Cliquez sur Confidentialité et choisissez Bloquer tous les cookies. Validez sur Ok.</li>
            <li><i class="fab fa-firefox-browser"></i> Sous Firefox : options / Vie privée et sécurité / Cookies et données de sites. Cliquez sur Effacez les données.</li>
        </ul>
    </div>

    <div class="col mt-4">
    <h2>Droit applicable et attribution de juridiction <i class="fas fa-balance-scale text-brun"></i></h2>

    Tout litige en relation avec l’utilisation du site <?php echo $GLOBALS['siteUrl']; ?> est soumis au droit français. L’utilisateur ainsi que <?php echo $GLOBALS['siteUrl']; ?> acceptent de se soumettre à la compétence exclusive des tribunaux Français en cas de litige.
    </div>

    <div class="col mt-4">
    <h2>Livre d'or <i class="fas fa-book text-warning"></i></h2>

    Seules les personnes majeures sont autorisées à écrire dans le livre d’or. <?php echo $GLOBALS['societe']; ?> contrôle l’ensemble des messages diffusés sur le livre d’or. Il peut en changer l’ordre et en supprimer si besoin. Il devra s’assurer que ceux-ci sont conformes avec les présentes CGU. Si des mineurs publient sur le livre d'or, ils devront le faire avec l’autorisation préalable, et sous le contrôle de leurs représentants légaux. La durée de conservation d'un message sera de 1 an. A l’issue de cette durée, le message sera supprimé.<br />
    Tout message qui pervertirait l’esprit du site en ayant des propos blessants, humiliants ou simplement moqueurs ou indélicats, sera susceptible d’être interdit de publication par <?php echo $GLOBALS['societe']; ?>. Par ailleurs, nous rappelons que la loi française sanctionne les délits d’injure et de diffamation, et qu’en conséquent, toute personne qui se rendrait coupable de tels méfaits engagerait sa responsabilité pénale. Conformément à l’article 27 de la loi du 12/06/2009 « Hadopi » et de l’article 6 de la loi du 21 juin 2004 pour la confiance dans l’économie numérique, la responsabilité de <?php echo $GLOBALS['societe']; ?> ne saurait être engagée dès lors qu’il n’a pas connaissance du message avant sa mise en ligne ou si, dès le moment où il en a eu connaissance, il a agi promptement pour retirer ce message. En conséquent, un système d’alerte permet à chaque internaute d’avertir le directeur de publication du caractère illicite d’un message afin que celui-ci procède à son retrait dans les plus brefs délais. Par ailleurs, tous propos à caractère raciste, xénophobe, homophobe, haineux, pornographique, violent ou contraire aux bonnes mœurs ; tous propos qui contrevient à l’obligation du secret professionnel, et généralement, tous propos à caractère illicite, sont interdits et leurs auteurs seront immédiatement interdit d’utilisation. Leurs auteurs seront poursuivis grâce à leur adresse IP qui est enregistrée au moment de la saisie du message. Conformément à l’article 323-1 du Code Pénal (Ordonnance n° 2000-916 du 19 septembre 2000 art. 3 Journal Officiel du 22 septembre 2000 en vigueur le 1er janvier 2002) qui précise, entre autre «que le fait d’accéder ou de se maintenir, frauduleusement, dans tout ou partie d’un système de traitement automatisé de données est puni de 2 ans d’emprisonnement et de 30000 euros d’amende.», l’utilisateur s’interdit de revenir via une autre connexion ou une autre adresse IP après avoir été averti que son message ne respecte pas les présentes CGU.
    </div>

    <div class="col mt-4">
    <h2>Bouteille à la mer <i class="fas fa-life-ring text-danger"></i> <i class="fas fa-wine-bottle text-success"></i></h2>

    "Une bouteille message" (ci-après dénommée) représente l'ensemble des données saisies par l'utilisateur au moment de la validation du formulaire.<br/>
    Seules les personnes majeures sont autorisées à "jeter une bouteille à la mer". <?php echo $GLOBALS['societe']; ?> contrôle l’ensemble des bouteilles messages postées. Il peut en supprimer si besoin. Il devra s’assurer que celles-ci sont conformes avec les présentes CGU. Si des mineurs utilisent ce formulaire, ils devront le faire avec l’autorisation préalable, et sous le contrôle de leurs représentants légaux. La durée de conservation d'une bouteille message sera de 1 an. A l’issue de cette durée, la bouteille message sera supprimée.<br />
    La page sert exclusivement à faire une demande pour un jeu qui ne se trouve pas dans le catalogue. Il n'y aucune obligation de la part de <?php echo $GLOBALS['societe']; ?> d'avoir à proposer une réponse dans le temps, cela ne sert que de suggestion de recherche. Toutefois, si un jeu semblable arrive dans le catalogue, l'utilisateur recevra un email avec une proposition correspondant au nouveau jeu entré en catalogue à l'adresse saisi, libre alors à l'utilisateur de confirmer ou non la proposition reçue par email.
    </div>

    <div class="col mt-4">
    <h2>Protection des biens et des personnes – gestion des données personnelles</h2>

    <?php echo $GLOBALS['societe']; ?> s'engage à ce que la collecte et le traitement de vos données, effectués à partir du site <?php echo $GLOBALS['siteUrl']; ?>, soient conformes au règlement général sur la protection des données (RGPD) et à la loi Informatique et Libertés.

    Chaque formulaire limite la collecte des données personnelles au strict nécessaire (minimisation des données).
    <p class="mt-4">
    <i class="fas fa-info"></i> En savoir plus auprès de la CNIL : <a rel="noreferrer" href="https://www.cnil.fr/fr/les-droits-pour-maitriser-vos-donnees-personnelles">cliquez ici</a>
    </p>

    Les données personnelles recueillies dans le cadre des services proposés sur <?php echo $GLOBALS['siteUrl']; ?> sont traitées selon des protocoles sécurisés et permettent à <?php echo $GLOBALS['societe']; ?> de gérer les demandes reçues dans ses applications informatiques.

    <p>
    Pour toute information ou exercice de vos droits Informatique et Libertés sur les traitements de données personnelles gérés par <?php echo $GLOBALS['societe']; ?>, vous pouvez contacter son responsable de protection des données (DPO) :
        <ul class="list-unstyled ml-2">
            <li><i class="fas fa-at"></i> Par le formulaire de contact <a href="/contact/">disponible sur cette page</a></li>
            <li><i class="fas fa-envelope"></i> Par courrier signé accompagné de la copie d’un titre d’identité à l'adresse suivante :</li>
        </ul>
                <div class="col ml-5">
                    <?php echo $GLOBALS['societe']; ?><br />
                    A l'attention du délégué à la protection des données (DPO)<br />
                    <?php echo $GLOBALS['adresseSociete']; ?>
                </div>
    </p>
    Sur le site <?php echo $GLOBALS['siteUrl']; ?>, le proprietaire du site ne collecte des informations personnelles relatives à l’utilisateur que pour le besoin de certains services proposés par le site <?php echo $GLOBALS['siteUrl']; ?>. L’utilisateur fournit ces informations en toute connaissance de cause, notamment lorsqu’il procède par lui-même à leur saisie. Il est alors précisé à l’utilisateur du site <?php echo $GLOBALS['siteUrl']; ?> l’obligation ou non de fournir ces informations.

    Aucune information personnelle de l’utilisateur du site <?php echo $GLOBALS['siteUrl']; ?> n’est publiée à l’insu de l’utilisateur, échangée, transférée, cédée ou vendue sur un support quelconque à des tiers sans la validation de l'utilisateur lui-même. Seule l’hypothèse du rachat du site <?php echo $GLOBALS['siteUrl']; ?> au proprietaire du site et de ses droits permettrait la transmission des dites informations à l’éventuel acquéreur qui serait à son tour tenu de la même obligation de conservation et de modification des données vis à vis de l’utilisateur du site <?php echo $GLOBALS['siteUrl']; ?>.
    </div>
</div>
<?php
include_once("../commun/bas_de_page.php");
?>
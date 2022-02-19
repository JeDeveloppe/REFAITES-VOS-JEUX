
<?php
@session_start ();
include_once("../config.php");
$titreDeLaPage = "Mentions légales  | ".$GLOBALS['titreDePage'];
$descriptionPage = "Nos conditions générales de ventes concernant le site.";
include_once("../commun/haut_de_page.php");
include_once("../commun/alertMessage.php");
?>

<div class="container-fluid mt-5">
    <h1 class="col mt-4 text-center">Conditions générales de vente</h1>
    <div class="col text-center small">Date de dernière mise à jour : 20/02/2022</div>

    <div class="col mt-4 mb-4"> 
        <div class="col mt-4 ml-4">
            <h2>Informations légales</h2>
                <p>
                Statut du propriétaire : Auto- entrepreneur<br/>
                Adresse : <?php echo $GLOBALS['adresseSociete']; ?><br/>
                SIRET : <?php echo $GLOBALS['siretSociete']; ?><br/>
                Adresse de courrier électronique : <?php echo $GLOBALS['adresseMailSite']; ?><br/>
                Responsable de la publication : <?php echo $GLOBALS['societe']; ?><br/>
                Le responsable de la publication est une personne physique<br />
                </p>
                <p>
                Créateur du site: <?php echo $GLOBALS['societe_creatrice_site']; ?><br />
                <i class="fas fa-pen"></i> Webmaster : <?php echo $GLOBALS['webmaster-responsableSite']; ?>
                </p>
        </div>

    <div class="col mt-5">
        <h2 class="col-12 col-md-10 mb-3">Article 1 - Objet</h2>

    Les présentes conditions régissent les ventes par <?php echo $GLOBALS['societe']; ?> de produits ou services commercialisés par le site internet <?php echo $GLOBALS['siteUrl']; ?>.
    </div>

    <div class="col mt-4">
        <h2 class="col-12 col-md-10 mb-3">Article 2 - Prix</h2>

    Les prix de nos produits sont indiqués en euros toutes taxes comprises (TVA et autres taxes applicables au jour de la commande), sauf indication contraire et hors frais de traitement et d'expédition.

    En cas de commande vers un pays autre que la France métropolitaine vous êtes l'importateur du ou des produits concernés. Des droits de douane ou autres taxes locales ou droits d'importation ou taxes d'état sont susceptibles d'être exigibles. Ces droits et sommes ne relèvent pas du ressort de <?php echo $GLOBALS['societe']; ?>. Ils seront à votre charge et relèvent de votre entière responsabilité, tant en termes de déclarations que de paiements aux autorités et organismes compétents de votre pays. Nous vous conseillons de vous renseigner sur ces aspects auprès de vos autorités locales.

    Toutes les commandes quelle que soit leur origine sont payables en euros.

    <?php echo $GLOBALS['societe']; ?> se réserve le droit de modifier ses prix à tout moment, mais le produit sera facturé sur la base du tarif en vigueur au moment de la validation de la commande et sous réserve de disponibilité.

    Les produits demeurent la propriété de <?php echo $GLOBALS['societe']; ?> jusqu'au paiement complet du prix.

    Attention : dès que vous prenez possession physiquement des produits commandés, les risques de perte ou d'endommagement des produits vous sont transférés.

    </div>

    <div class="col mt-4">
        <h2 class="col-12 col-md-10 mb-3">Article 3 - Commandes</h2>

    Vous pouvez passer commande uniquement sur internet : <?php echo $GLOBALS['siteUrl']; ?>

    Les informations contractuelles sont présentées en langue française et feront l'objet d'une confirmation au plus tard au moment de la validation de votre commande.

    <?php echo $GLOBALS['societe']; ?> se réserve le droit de ne pas enregistrer un paiement, et de ne pas confirmer une commande pour quelque raison que ce soit, et plus particulièrement en cas de problème d'approvisionnement, ou en cas de difficulté concernant la commande reçue.
    </div>

    <div class="col mt-4">
        <h2 class="col-12 col-md-10 mb-3">Article 4 - Validation de votre commande</h2>

    Toute commande figurant sur le site Internet <?php echo $GLOBALS['siteUrl']; ?> suppose l'adhésion aux présentes Conditions Générales. Toute confirmation de commande entraîne votre adhésion pleine et entière aux présentes conditions générales de vente, sans exception ni réserve.

    L'ensemble des données fournies et la confirmation enregistrée vaudront preuve de la transaction.

    Vous déclarez en avoir parfaite connaissance.

    La confirmation de commande vaudra signature et acceptation des opérations effectuées.

    </div>

    <div class="col mt-4">
        <h2 class="col-12 col-md-10 mb-3">Article 5 - Paiement</h2>

    Le fait de valider votre commande implique pour vous l'obligation de payer le prix indiqué.<br/>

    Voici les moyens de paiement disponibles sur le site <?php echo $GLOBALS['siteUrl']; ?> :
        <ul>
            <li>Paiement par carte bancaire. Le débit de la carte est nécessaire pour confirmer la commande.</li>
        </ul>
    </div>

    <div class="col mt-4">
        <h2 class="col-12 col-md-10 mb-3">Article 6 - Rétractation</h2>

    Conformément aux dispositions de l'article L.121-21 du Code de la Consommation, vous disposez d'un délai de rétractation de 14 jours à compter de la réception de vos produits pour exercer votre droit de rétraction sans avoir à justifier de motifs ni à payer de pénalité.

    Les retours sont à effectuer dans un état semblable à l'expédition. Dans ce cadre, votre responsabilité est engagée. Tout dommage subi par le produit à cette occasion peut être de nature à faire échec au droit de rétractation.

    Les frais de retour sont à votre charge.

    En cas d'exercice du droit de rétractation, <?php echo $GLOBALS['societe']; ?> procédera au remboursement des sommes versées, dans un délai de 14 jours suivant la notification de votre demande et via le même moyen de paiement que celui utilisé lors de la commande.

    <p class="mt-4">EXCEPTIONS AU DROIT DE RETRACTATION</p>

    Conformément aux dispositions de l'article L.121-21-8 du Code de la Consommation, le droit de rétractation ne s'applique pas à :
        <ul>
            <li>La fourniture de services pleinement exécutés avant la fin du délai de rétractation et dont l'exécution a commencé après accord préalable exprès du consommateur et renoncement exprès à son droit de rétractation.</li>
            <li>La fourniture de biens ou de services dont le prix dépend de fluctuations sur le marché financier échappant au contrôle du professionnel et susceptibles de se produire pendant le délai de rétractation.</li>
            <li>La fourniture de biens confectionnés selon les spécifications du consommateur ou nettement personnalisés.</li>
            <li>La fourniture de biens susceptibles de se détériorer ou de se périmer rapidement.</li>
            <li>La fourniture de biens qui ont été descellés par le consommateur après la livraison et qui ne peuvent être renvoyés pour des raisons d'hygiène ou de protection de la santé.</li>
            <li>La fourniture de biens qui, après avoir été livrés et de par leur nature, sont mélangés de manière indissociable avec d'autres articles.</li>
            <li>La fourniture de boissons alcoolisées dont la livraison est différée au-delà de trente jours et dont la valeur convenue à la conclusion du contrat dépend de fluctuations sur le marché échappant au contrôle du professionnel.</li>
            <li>La fourniture d'enregistrements audio ou vidéo ou de logiciels informatiques lorsqu'ils ont été descellés par le consommateur après la livraison.</li>
            <li>La fourniture d'un journal, d'un périodique ou d'un magazine, sauf pour les contrats d'abonnement à ces publications.</li>
            <li>Les transactions conclues lors d'une enchère publique.</li>
            <li>La fourniture d'un contenu numérique non fourni sur un support matériel dont l'exécution a commencé après accord préalable exprès du consommateur et renoncement exprès à son droit de rétractation.</li>
        </ul>
    </div>

    <div class="col mt-4">
        <h2 class="col-12 col-md-10 mb-3">Article 7 - Livraison</h2>

    Les produits sont livrés à l'adresse de livraison indiquée au cours du processus de commande.

    Les délais d'expedition indiqués sont indicatifs. <?php echo $GLOBALS['societe']; ?> s'engage à livrer le bien ou exécuter la prestation sans retard injustifié et au plus tard trente jours après la commande.

    En cas de retard d'expédition, un mail vous sera adressé pour vous informer d'une éventuelle conséquence sur le délai de livraison qui vous a été indiqué.

    Conformément aux dispositions légales, en cas de retard de livraison, vous bénéficiez de la possibilité d'annuler la commande dans les conditions et modalités définies à l'article L 138-2 du Code de la Consommation. Si entre temps vous recevez le produit, nous procéderons à son remboursement et aux frais de retour dans les conditions de l'article L 138-3 du Code de la Consommation.

    En cas de livraison par un transporteur, <?php echo $GLOBALS['societe']; ?> ne peut être tenue pour responsable de retard de livraison dû exclusivement à une indisponibilité du client après plusieurs propositions de rendez-vous.
    </div>

    <div class="col mt-4">
        <h2 class="col-12 col-md-10 mb-3">Article 8 - Responsabilité</h2>

    Les produits proposés sont conformes à la législation française en vigueur. La responsabilité de <?php echo $GLOBALS['societe']; ?> ne saurait être engagée en cas de non-respect de la législation du pays où le produit est livré. Il vous appartient de vérifier auprès des autorités locales les possibilités d'importation ou d'utilisation des produits ou services que vous envisagez de commander.

    Par ailleurs, <?php echo $GLOBALS['societe']; ?> ne saurait être tenue pour responsable des dommages résultant d'une mauvaise utilisation du produit acheté.

    Enfin la responsabilité de <?php echo $GLOBALS['societe']; ?> ne saurait être engagée pour tous les inconvénients ou dommages inhérents à l'utilisation du réseau Internet, notamment une rupture de service, une intrusion extérieure ou la présence de virus informatiques.
    </div>

    <div class="col mt-4">
        <h2 class="col-12 col-md-10 mb-3">Article 9 - Droit applicable en cas de litiges</h2>

    La langue du présent contrat est la langue française. Les présentes conditions de vente sont soumises à la loi française. En cas de litige, les tribunaux français seront les seuls compétents.
    </div>

    <div class="col mt-4">
        <h2 class="col-12 col-md-10 mb-3">Article 10 - Propriété intellectuelle</h2>

    Tous les éléments du site <?php echo $GLOBALS['siteUrl']; ?> sont et restent la propriété intellectuelle et exclusive de <?php echo $GLOBALS['societe']; ?>. Nul n'est autorisé à reproduire, exploiter, rediffuser, ou utiliser à quelque titre que ce soit, même partiellement, des éléments du site qu'ils soient logiciels, visuels ou sonores.
    </div>

    <div class="col mt-4">
        <h2 class="col-12 col-md-10 mb-3">Article 11 - Données personnelles</h2>

    <?php echo $GLOBALS['societe']; ?> se réserve le droit de collecter les informations nominatives et les données personnelles vous concernant. Elles sont nécessaires à la gestion de votre commande, ainsi qu'à l'amélioration des services et des informations que nous vous adressons.

    Elles peuvent aussi être transmises aux sociétés qui contribuent à ces relations, telles que celles chargées de l'exécution des services et commandes pour leur gestion, exécution, traitement et paiement.

    Ces informations et données sont également conservées à des fins de sécurité, afin de respecter les obligations légales et réglementaires.

    Conformément à la loi du 6 janvier 1978, vous disposez d'un droit d'accès, de rectification et d'opposition aux informations nominatives et aux données personnelles vous concernant, directement sur le site Internet.
    </div>

    <div class="col mt-4">
        <h2 class="col-12 col-md-10 mb-3">Article 12 - Archivage Preuve</h2>
    <?php echo $GLOBALS['societe']; ?> archivera les bons de commandes et les factures sur un support fiable et durable constituant une copie fidèle conformément aux dispositions de l'article 1348 du Code civil.

    Les registres informatisés de <?php echo $GLOBALS['societe']; ?> seront considérés par toutes les parties concernées comme preuve des communications, commandes, paiements et transactions intervenus entre les parties.
    </div>

</div>
<?php
include_once("../commun/bas_de_page.php");
?>
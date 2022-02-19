<!-- Javascript library. Should be loaded in head section -->
<script src="'.$client->getClientEndpoint().'/static/js/krypton-client/V4.0/stable/kr-payment-form.min.js"
kr-public-key="'.$client->getPublicKey().'"
kr-post-url-success="paid.php">
</script>
<!-- theme and plugins. should be loaded after the javascript library -->
<!-- not mandatory but helps to have a nice payment form out of the box -->
<link rel="stylesheet" href="'.$client->getClientEndpoint().'/static/js/krypton-client/V4.0/ext/classic-reset.css">
<script src="'.$client->getClientEndpoint().'/static/js/krypton-client/V4.0/ext/classic.js"></script>
<?php
$contentMail ='
<!-- PARAGRAPH -->
<!-- Set text color and font family ("sans-serif" or "Georgia, serif"). Duplicate all text styles in links, including line-height -->
<tr>
    <td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 87.5%; font-size: 17px; font-weight: 400; line-height: 160%;
        padding-top: 25px; 
        color: #000000;
        font-family: sans-serif;" class="paragraph">
        <p>Bonjour,</p><p>Merci pour votre inscription !</p>
        <p>Pour la valider, cliquez sur le bouton suivant: </p>
    </td>
</tr>

<!-- BUTTON -->
	<tr>
		<td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 87.5%;
			padding-top: 25px;
			padding-bottom: 5px;" class="button"><a
			href="'.$GLOBALS['domaine'].'/membre/controles/verifValidationInscription.php?mail='.$mailDestinataire.'&validKey='.$validKey.'" target="_blank" style="text-decoration: none;">
				<table border="0" cellpadding="0" cellspacing="0" align="center" style="max-width: 240px; min-width: 120px; border-collapse: collapse; border-spacing: 0; padding: 0;"><tr><td align="center" valign="middle" style="padding: 12px 24px; margin: 0; text-decoration: none; border-collapse: collapse; border-spacing: 0; border-radius: 4px; -webkit-border-radius: 4px; -moz-border-radius: 4px; -khtml-border-radius: 4px;"
					bgcolor="#30880b"><a target="_blank" style="text-decoration: none;
					color: #FFFFFF; font-family: sans-serif; font-size: 17px; font-weight: 400; line-height: 120%;"
					href="'.$GLOBALS['domaine'].'/membre/controles/verifValidationInscription.php?mail='.$mailDestinataire.'&validKey='.$validKey.'" target="_blank" style="text-decoration: none;">
						Je valide l\'inscription !
					</a>
			</td></tr></table></a>
		</td>
    </tr>

<!-- PARAGRAPH -->
<!-- Set text color and font family ("sans-serif" or "Georgia, serif"). Duplicate all text styles in links, including line-height -->
<tr>
    <td align="left" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 87.5%; font-size: 17px; font-weight: 400; line-height: 160%;
        padding-top: 25px; 
        color: #000000;
        font-family: sans-serif;" class="paragraph">
        Si le lien ne fonctionne pas, copiez et coller ce texte dans votre navigateur web favori:
    </td>
</tr>

<!-- PARAGRAPH -->
<!-- Set text color and font family ("sans-serif" or "Georgia, serif"). Duplicate all text styles in links, including line-height -->
<tr>
    <td align="left" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 87.5%; font-size: 14px; font-weight: 400; line-height: 160%;
        padding-top: 25px; 
        color: #000000;
        font-family: sans-serif;" class="paragraph">
        '.$GLOBALS['domaine'].'/membre/controles/verifValidationInscription.php?mail='.$mailDestinataire.'&validKey='.$validKey.'
    </td>
</tr>';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture</title>
    <link rel="stylesheet" href="/style/style.css">
</head>
<body class="genFacture">
    <div class="invoice">
        <section>
            <article class="issued-to">
                <h2>Délivré à</h2>
                <p>
                    {Nom} {Prénom}<br>
                    {Dénomination sociale}<br>
                    mail@example.com
                </p>
            </article>
            <article>
                <h2>Numéro de facture</h2>
                <h2>#012345</h2>
                <p>{jj/mm/aaaa}</p>
            </article>
        </section>
        <section class="invoice-details">
            <table>
                <hr>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Qté</th>
                        <th>Prix HT</th>
                        <th>Prix Unitaire</th>
                        <th>Prix TTC</th>
                        <th>% TVA</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <hr>
                <tbody>
                    <tr>
                        <td>Option relief</td>
                        <td>2</td>
                        <td>6.50€</td>
                        <td>6.50€</td>
                        <td>22.30€</td>
                        <td>20%</td>
                        <td>15/04/2024</td>
                    </tr>
                    <tr>
                        <td>Offre premium</td>
                        <td>1</td>
                        <td>24.75€</td>
                        <td>24.75€</td>
                        <td>5.10€</td>
                        <td>20%</td>
                        <td>17/04/2024</td>
                    </tr>
                </tbody>
            </table>
        </section>
        <article class="totals">
            <hr>
            <p>Total HT: 31.25€</p>
            <p>Total TVA: 10€</p>
            <p>Total TTC: 34.38€</p>
            <hr>
        </article>

        <article class="payment-terms">
            <h2>Conditions et modalités de paiement</h2>
            <p>Le paiement est dû dans les 15 jours</p>
            <p>
                Nom Banque<br>
                Nom du compte: Nom<br>
                Numéro de compte : 123-456-7890
            </p>
        </article>
    </div>
</body>
</html>

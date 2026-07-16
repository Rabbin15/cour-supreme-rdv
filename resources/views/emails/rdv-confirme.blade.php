<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rendez-vous Cour Suprême</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f7fa; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .header { text-align: center; border-bottom: 3px solid #f5c842; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { color: #0a1a3a; margin: 0; }
        .status { font-size: 1.2rem; font-weight: bold; padding: 10px; border-radius: 8px; text-align: center; margin: 20px 0; }
        .status.accepte { background: #d4edda; color: #155724; }
        .status.refuse { background: #f8d7da; color: #721c24; }
        .details { background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 20px 0; }
        .details p { margin: 8px 0; }
        .footer { text-align: center; color: #888; font-size: 0.9rem; margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏛️ Cour Suprême du Bénin</h1>
            <p>Gestion des rendez-vous administratifs</p>
        </div>

        <p>Bonjour <strong>{{ $rdv->nom_complet }}</strong>,</p>

        <div class="status {{ $statut === 'accepte' ? 'accepte' : 'refuse' }}">
            @if($statut === 'accepte')
                ✅ Votre rendez-vous a été <strong>ACCEPTÉ</strong> !
            @else
                ❌ Votre rendez-vous a été <strong>REFUSÉ</strong>.
            @endif
        </div>

        @if($statut === 'refuse' && $rdv->motif_refus)
            <p><strong>Motif du refus :</strong> {{ $rdv->motif_refus }}</p>
        @endif

        <div class="details">
            <h4>📋 Détails de votre demande :</h4>
            <p><strong>Structure :</strong> {{ $rdv->structure->nom_structure }}</p>
            <p><strong>Agent :</strong> {{ $rdv->creneau->agent->prenom }} {{ $rdv->creneau->agent->nom }}</p>
            <p><strong>Jour :</strong> {{ $rdv->creneau->jour_semaine }}</p>
            <p><strong>Heure :</strong> {{ $rdv->creneau->heure_debut }} - {{ $rdv->creneau->heure_fin }}</p>
            <p><strong>Motif :</strong> {{ $rdv->motif }}</p>
            <p><strong>Numéro de dossier :</strong> #{{ $rdv->id_rdv }}</p>
        </div>

        @if($statut === 'accepte')
            <p>📌 <strong>Instructions :</strong> Veuillez vous présenter 10 minutes avant l'heure prévue à l'accueil de la Cour Suprême.</p>
        @else
            <p>📌 <strong>Instructions :</strong> Vous pouvez prendre un nouveau rendez-vous à une autre date ou contacter la structure concernée pour plus d'informations.</p>
        @endif

        <p>Cordialement,</p>
        <p><strong>Cour Suprême du Bénin</strong></p>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Cour Suprême du Bénin - Tous droits réservés</p>
        </div>
    </div>
</body>
</html>
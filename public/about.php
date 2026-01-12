<?php
session_start();
$title = "Qui sommes-nous - K-Store";
require_once __DIR__ . '/../includes/header.php';
?>

<header class="container hero">
  <div class="badge">
    <span class="flag">🇰🇷</span>
    <span class="hangul">케이스토어</span>
    <span class="dot">•</span>
    <span class="subtitle">K-Store KR</span>
  </div>

  <h1>Qui sommes-nous ?</h1>
  <p>Un coin de Corée — ramen, snacks, k-beauty et vibes de Séoul ✨</p>
</header>

<main class="container about">

  <section class="about-grid">
    <article class="panel">
      <h2>🌸 Notre univers</h2>
      <p>
        K-Store, c’est une boutique qui célèbre la Corée moderne :
        saveurs iconiques, petites découvertes, et essentiels k-beauty.
        Une expérience simple, élégante, et inspirée des rues de Séoul la nuit.
      </p>

      <div class="chips">
        <span class="chip">🍜 Ramen</span>
        <span class="chip">🍪 Snacks</span>
        <span class="chip">🥤 Boissons</span>
        <span class="chip">💄 K-Beauty</span>
        <span class="chip">🎵 K-Pop</span>
      </div>
    </article>

    <article class="panel">
      <h2>💜 Notre promesse</h2>
      <ul class="list">
        <li><span class="bullet">✓</span> Une sélection claire et organisée par catégories</li>
        <li><span class="bullet">✓</span> Un panier simple avec quantités et total automatique</li>
        <li><span class="bullet">✓</span> Un compte client pour suivre ses commandes</li>
        <li><span class="bullet">✓</span> Une interface admin pour gérer le catalogue</li>
      </ul>

      <div class="callout">
        <strong>Fun fact :</strong> “케이스토어” se lit <em>Ke-i-seu-to-eo</em> 😉
      </div>
    </article>
  </section>

  <section class="story panel">
    <h2>🌙 L’histoire K-Store</h2>
    <p>
      Tout part d’une idée simple : retrouver l’énergie des supérettes coréennes
      (les convenience stores), les néons, les nouveautés, et cette sensation
      de “je teste juste un truc”… qui finit en panier plein.
    </p>
    <p>
      Ici, chaque produit est une petite porte d’entrée vers la culture pop,
      les tendances beauté et les goûts qui font la différence.
    </p>
  </section>

  <section class="panel contact">
    <h2>📍 Nous contacter</h2>
    <p>
      Une question, une suggestion, une idée produit ?
      Écris-nous — on adore les recommandations !
    </p>

    <div class="contact-grid">
      <div class="contact-item">
        <div class="contact-label">Email</div>
        <div class="contact-value">contact@kstore.local</div>
      </div>
      <div class="contact-item">
        <div class="contact-label">Instagram</div>
        <div class="contact-value">@kstore.kr</div>
      </div>
      <div class="contact-item">
        <div class="contact-label">Horaires</div>
        <div class="contact-value">Lun–Sam • 10h–19h</div>
      </div>
    </div>

    <div class="cta-row">
      <a class="btn" href="/-e-commerce-dynamique/public/items.php">Découvrir le catalogue →</a>
      <a class="btn ghost" href="/-e-commerce-dynamique/public/cart.php">Voir mon panier 🛒</a>
    </div>
  </section>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

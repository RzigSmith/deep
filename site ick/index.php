<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Site ick</title>
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <header>
      <div class="logo">
        <h2>Smith<span>Collection</span></h2>
      </div>
      <nav>
        <ul>
          <li><a href="#" class="active">Accuille</a></li>
          <li><a href="#">Produits</a></li>
          <li><a href="#">Connectez-Vous</a></li>
          <li><a href="#">Service</a></li>
          <li><a href="#">Contacte</a></li>
        </ul>
      </nav>
    </header>

    <section class="hero">
      <div class="text-container">
        <h1>Shoes<br /><span>Smill</span></h1>
        <p>
          Lorem ipsum, dolor sit amet consectetur adipisicing elit. Ut unde
          tenetur blanditiis quod. Vero dolorum corrupti et numquam
          exercitationem nulla mollitia asperiores amet non qui quam impedit
          consequuntur dolores ea porro odit voluptas at neque deleniti facere,
          consectetur veritatis ducimus fugiat voluptatum. Tempore temporibus
          animi maiores accusantium nisi reprehenderit, ea quo aliquam debitis
          quas nemo, voluptate eum voluptates cumque hic inventore ad veritatis
          est beatae. Laborum sequi debitis ab cupiditate ad maiores amet ea
          fugiat, quidem sapiente perspiciatis eius sit deserunt cumque facilis,
          facere molestias, ex corrupti harum tempora quos.
        </p>
        <a href="#" class="button">ACHETEZ</a>
      </div>

      <div class="image-container">
        <img src="image/1.png" alt="Shoe" class="shoe-image" width="450px" />
        <div class="Produit-icons">
          <div class="icon active" data-shoe="1">
            <img src="image/1.png" width="60" />
          </div>
          <div class="icon" data-shoe="2">
            <img src="image/2.png" width="60" />
          </div>
          <div class="icon" data-shoe="3">
            <img src="image/3.png" width="60" />
          </div>
          <div class="icon" data-shoe="4">
            <img src="image/4.png" width="60" />
          </div>
        </div>
      </div>
      <div class="more-button">
        <span>More</span>
        <div class="arrow"></div>
      </div>
    </section>

    <script src="script.js"></script>
  </body>
</html>

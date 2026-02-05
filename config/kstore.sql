-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 05 fév. 2026 à 09:07
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `kstore`
--

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(6, 'Accessoires'),
(3, 'Boissons'),
(8, 'Épicerie'),
(7, 'Goodies'),
(4, 'K-Beauty'),
(5, 'K-Pop'),
(9, 'Maison & Lifestyle'),
(1, 'Ramen'),
(2, 'Snacks');

-- --------------------------------------------------------

--
-- Structure de la table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `amount` decimal(10,2) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(120) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `country` varchar(80) NOT NULL DEFAULT 'France'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(160) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `restock_at` date DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `published_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `items`
--

INSERT INTO `items` (`id`, `category_id`, `name`, `description`, `price`, `stock`, `restock_at`, `image`, `is_active`, `published_at`) VALUES
(1, 1, 'Ramen Samyang Hot Chicken', 'Préparez-vous à une explosion de saveurs avec les légendaires Buldak Bokkeum Myeon. Reconnues mondialement pour leur piquant extrême, ces nouilles sautées coréennes ne sont pas seulement un repas, c\'est une véritable expérience sensorielle pour les amateurs de sensations fortes.\r\n\r\nCe qui vous attend dans ce paquet :\r\n\r\nLe Goût \"Buldak\" Iconique : Une sauce onctueuse au goût de poulet rôti, enrichie d\'un mélange secret d\'épices coréennes qui offre une saveur sucrée-salée avant de laisser place à une chaleur intense.\r\n\r\nNouilles \"Ramyun\" Premium : Des nouilles épaisses, larges et parfaitement élastiques qui retiennent idéalement la sauce pour une texture satisfaisante en bouche.\r\n\r\nGarniture Croustillante : Chaque sachet contient un petit sachet de graines de sésame grillées et d\'éclats d\'algues nori pour apporter une touche finale authentique et parfumée.\r\n\r\nLe Phénomène \"Fire Noodle Challenge\" : Devenu viral sur Internet, ce produit est la référence absolue pour tester votre résistance au piquant.\r\n\r\nConseil de préparation (Style Coréen) : Après avoir égoutté les nouilles, faites-les sauter 30 secondes avec la sauce pour une caramélisation parfaite. Pour une version plus gourmande et légèrement moins épicée, ajoutez une tranche de fromage fondu ou un œuf au plat sur le dessus.', 3.50, 30, '2026-02-01', 'samyang.jpg', 1, '2026-01-12 11:15:37'),
(3, 2, 'Choco Pie', 'Plus qu’un simple biscuit, le Choco Pie est une véritable institution culturelle. Ce petit gâteau individuel est le compagnon idéal de vos pauses thé ou café, offrant un équilibre parfait entre moelleux et onctuosité.\r\n\r\nLes trois couches du bonheur :\r\n\r\nLe Biscuit Moelleux : Deux disques de génoise légère et aérée qui fondent délicatement en bouche.\r\n\r\nLe Cœur de Guimauve (Marshmallow) : Un centre blanc comme neige, élastique et fondant, qui apporte une texture unique et une douceur incomparable.\r\n\r\nL’Enrobage Chocolaté : Une fine couche de chocolat craquant qui enveloppe le tout pour une finition gourmande et équilibrée.\r\n\r\nPourquoi est-il si spécial ?\r\n\r\nUn Goût d\'Enfance : En Corée, le Choco Pie est associé au concept de \"Jeong\" (le sentiment d\'affection et de lien social). C’est le cadeau que l’on offre pour montrer que l’on tient à quelqu’un.\r\n\r\nSans Conservateurs Artificiels : Une recette travaillée pour conserver sa fraîcheur et son moelleux sans compromis sur la qualité.\r\n\r\nFormat Pratique : Emballé individuellement dans son sachet fraîcheur, il se glisse partout : dans le sac, le tiroir du bureau ou la boîte à goûter des enfants.\r\n\r\nL\'astuce gourmande : Pour une expérience encore plus réconfortante, passez votre Choco Pie 5 à 10 secondes au micro-ondes. Le cœur de guimauve va gonfler et devenir coulant... un pur délice à la petite cuillère !', 4.20, 25, NULL, 'chocopie.jpg', 1, '2026-01-12 11:15:37'),
(4, 2, 'Pepero Original', 'Le Pepero Original de Lotte est bien plus qu\'un simple biscuit : c\'est une véritable icône de la culture pop coréenne. Symbole d\'amitié et d\'amour, ce bâtonnet est si populaire qu\'il possède même sa propre fête nationale en Corée, le \"Pepero Day\" (le 11 novembre).\r\n\r\nCe qui fait son succès :\r\n\r\nLe Biscuit \"Pretzel\" : Un bâtonnet de biscuit longiligne, parfaitement cuit pour obtenir un craquant net et léger à chaque bouchée.\r\n\r\nL\'Enrobage Chocolat Premium : Un chocolat au lait onctueux et riche qui recouvre le biscuit sur presque toute sa longueur, laissant juste assez de place au bout pour le tenir sans se salir les doigts.\r\n\r\nL\'Équilibre Parfait : Un ratio idéal entre le croquant du biscuit sec et la douceur fondante du chocolat, offrant une collation qui n\'est jamais trop sucrée.\r\n\r\nPourquoi on l\'adore :\r\n\r\nLe Snack \"On-the-go\" : Son format fin et sa boîte rigide permettent de l\'emporter partout sans casser les bâtonnets. C\'est le compagnon idéal pour une pause rapide ou devant une série.\r\n\r\nPartage et Convivialité : Conçu pour être partagé, chaque boîte contient plusieurs bâtonnets, invitant à la générosité avec ses amis ou collègues.\r\n\r\nPolyvalence : Excellent seul, il est aussi souvent utilisé en décoration sur des gâteaux, des glaces ou trempé dans un grand verre de lait froid.', 2.50, 50, NULL, 'pepero.jpg', 1, '2026-01-12 11:15:37'),
(5, 3, 'Boisson Aloe Vera', 'Plongez dans une expérience de désaltération pure avec cette boisson à l\'aloe vera. Alliant les bienfaits d\'une plante millénaire à un plaisir gustatif moderne, c\'est l\'alternative parfaite aux sodas trop sucrés pour ceux qui recherchent une boisson à la fois saine et gourmande.\r\n\r\nCe qui la rend unique :\r\n\r\nVrais Morceaux de Pulpe : Contrairement aux boissons classiques, celle-ci contient des cubes de gel d\'aloe vera frais. Ils apportent une texture croquante et ludique qui rend chaque gorgée unique.\r\n\r\nHydratation Maximale : Naturellement riche en eau et en nutriments, l\'aloe vera aide à revitaliser le corps et à hydrater la peau de l\'intérieur.\r\n\r\nSaveur Douce et Florale : Un goût délicat, légèrement sucré avec une pointe de miel ou de raisin, qui laisse une sensation de propreté et de fraîcheur en bouche.\r\n\r\nBienfaits Digestifs : Reconnue pour ses propriétés apaisantes sur le système digestif, c\'est la boisson idéale après un repas épicé (comme des Ramen Buldak !).\r\n\r\nPourquoi vous allez l\'adopter :\r\n\r\nSans Colorants Artificiels : Une robe translucide qui reflète la pureté des ingrédients naturels.\r\n\r\nBouteille Ergonomique : Son format iconique est facile à tenir et parfait pour vous accompagner à la salle de sport, au bureau ou en balade.\r\n\r\nServir Très Frais : C\'est glacée qu\'elle révèle tout son potentiel rafraîchissant.\r\n\r\nConseil dégustation : Agitez légèrement la bouteille avant de l\'ouvrir pour bien répartir les morceaux de pulpe d\'aloe vera et profiter de toutes les textures dès la première gorgé', 2.20, 34, NULL, 'aloe.jpg', 1, '2026-01-12 11:15:37'),
(6, 3, 'Soda Milkis', 'Découvrez le Milkis, une boisson gazeuse coréenne emblématique qui défie les conventions des sodas classiques. Son goût unique et rafraîchissant, à la fois crémeux et pétillant, en a fait une favorite en Corée et bien au-delà. C\'est le mélange parfait entre une limonade légère et une touche de lait pour une expérience gustative inattendue.\r\n\r\nCe qui le rend si unique :\r\n\r\nL\'Alliance Surprenante : Le Milkis combine la légèreté d\'un soda pétillant avec une subtile onctuosité lactée, rappelant parfois une crème glacée fondue ou un yaourt à boire gazéifié.\r\n\r\nDouceur Équilibrée : Sa saveur légèrement sucrée et son arrière-goût doux le rendent extrêmement désaltérant sans être écœurant.\r\n\r\nGoût \"Soft Drink\" Reconfortant : C\'est une boisson qui évoque la nostalgie et la simplicité, souvent appréciée pour son côté réconfortant.\r\n\r\nSans Colorants Artificiels : Sa couleur blanche laiteuse est naturelle, provenant du lait ou du lait écrémé en poudre qu\'elle contient.\r\n\r\nPourquoi l\'essayer ?\r\n\r\nExpérience Originale : Si vous aimez les nouvelles saveurs et les boissons uniques, le Milkis est un incontournable.\r\n\r\nIdéal pour le Goûter : Il accompagne parfaitement les snacks salés ou sucrés et est très apprécié des enfants comme des adultes.\r\n\r\nServir Très Frais : C\'est glacé que le Milkis révèle toute sa puissance rafraîchissante et que son mélange crémeux-pétillant est le plus agréable.', 2.00, 45, NULL, 'milkis.jpg', 1, '2026-01-12 11:15:37'),
(7, 4, 'Masque visage Innisfree', 'Découvrez le pouvoir purifiant et régénérant de la nature avec les masques en tissu Innisfree, une marque coréenne pionnière en matière de cosmétiques naturels et éco-responsables. Formulés à partir d\'ingrédients frais et sains provenant de l\'île volcanique préservée de Jeju, ces masques sont une invitation à un rituel de soin apaisant et efficace.\r\n\r\nCe qui le rend exceptionnel :\r\n\r\nIngrédients d\'Origine Naturelle de Jeju : Chaque masque est infusé d\'extraits végétaux spécifiques (thé vert, grenade, miel, concombre, etc.) cultivés sur l\'île de Jeju, réputée pour sa terre fertile et son environnement pur.\r\n\r\nTechnologie \"Real Squeeze\" : Innisfree utilise une méthode d\'extraction à froid pour préserver au maximum les nutriments et l\'efficacité des ingrédients, garantissant ainsi un sérum riche et concentré.\r\n\r\nTissu Respirant et Écologique : Fabriqué à partir de cellulose naturelle, le tissu du masque est fin, doux et adhère parfaitement aux contours du visage, permettant une absorption optimale du sérum sans irriter la peau. Il est également biodégradable.\r\n\r\nFormulation Personnalisée : Disponible en trois types de texture d\'essence (eau, crème, ampoule) pour s\'adapter à chaque type de peau et à chaque besoin spécifique (hydratation, apaisement, éclat, fermeté).\r\n\r\nPourquoi l\'intégrer à votre routine beauté ?\r\n\r\nHydratation Intense : Nourrit la peau en profondeur, lui redonnant souplesse et éclat.\r\n\r\nSoin Ciblée : Que vous cherchiez à apaiser les peaux sensibles, à réduire l\'excès de sébum ou à combattre les signes de l\'âge, il existe un masque Innisfree pour vous.\r\n\r\nMoment de Détente : L\'application du masque est un véritable instant de bien-être, parfait pour décompresser après une longue journée.\r\n\r\nEngagement Éthique : Innisfree s\'engage pour la beauté verte, avec des emballages recyclables et une production respectueuse de l\'environnement.', 2.80, 60, NULL, 'mask.jpg', 1, '2026-01-12 11:15:37'),
(8, 5, 'Album K-Pop (édition standard)', 'Découvrez ou redécouvrez la magie de la K-Pop avec l\'édition standard d\'un album. Plus qu\'un simple recueil de chansons, c\'est une œuvre d\'art pensée pour immerger les fans dans l\'univers de leur groupe préféré, offrant une collection de contenus exclusifs qui capturent l\'essence de leur musique et de leur esthétique.\r\n\r\nCe que vous trouverez dans chaque album (édition standard) :\r\n\r\nLe CD Musical : Contenant tous les titres de l\'album, y compris la chanson titre, des B-sides inédites, et souvent des interludes ou instrumentaux, pour une écoute haute fidélité.\r\n\r\nLe Photobook Exclusif : Un livre richement illustré avec des photos de haute qualité des membres du groupe. Ces clichés, souvent thématiques, capturent l\'ambiance visuelle du concept de l\'album, avec des styles et des décors variés.\r\n\r\nPhotocards Aléatoires : L\'un des trésors les plus recherchés ! Chaque album contient une ou plusieurs photocards (cartes photo) aléatoires des membres, créant une surprise à l\'ouverture et encourageant les échanges entre fans pour compléter leur collection.\r\n\r\nLyrics Book (Livret de Paroles) : Un livret élégant présentant les paroles des chansons, souvent agrémenté de typographies artistiques et de graphismes en lien avec le concept.\r\n\r\nDesign et Packaging Soignés : L\'extérieur de l\'album est une œuvre d\'art en soi, avec des illustrations, des finitions mates ou brillantes, et un design qui reflète l\'esthétique générale de la sortie musicale.\r\n\r\nPourquoi collectionner les albums K-Pop ?\r\n\r\nSoutenir Vos Idoles : L\'achat d\'albums est l\'un des moyens les plus directs de soutenir les artistes, contribuant à leurs classements dans les charts et à leur reconnaissance.\r\n\r\nExpérience Immersive : Il permet de s\'immerger totalement dans l\'univers artistique du groupe, en reliant la musique, les visuels et le message de l\'album.\r\n\r\nObjet de Collection : Chaque album est unique, avec des variations de pochettes, de photocards et d\'éléments bonus, le rendant précieux pour les collectionneurs.\r\n\r\nQualité Supérieure : Les standards de production élevés garantissent un produit de haute qualité, tant au niveau sonore que visuel.', 18.90, 100, NULL, 'album.jpg', 1, '2026-01-12 11:15:37'),
(9, 6, 'Porte-clés K-Store (Hanbok)', 'Le porte-clés K-Store est bien plus qu\'un simple accessoire de rangement pour vos clés. C\'est un petit bijou de design qui fusionne l\'esthétique \"Kawaii\" et l\'élégance moderne de la K-Culture. Conçu pour être accroché aussi bien à vos clés qu\'à votre sac à dos ou votre trousse, il est le signe de ralliement de tous les passionnés.\r\n\r\nLes détails qui font la différence :\r\n\r\nDesign Exclusif : Chaque modèle arbore des symboles iconiques, que ce soit des personnages mignons (inspirés des mascottes de groupes), des logos stylisés en relief ou des inscriptions en Hangul (alphabet coréen) élégantes.\r\n\r\nMatériaux de Haute Qualité : Fabriqué en silicone souple haute densité ou en acrylique poli, il résiste aux chocs et aux rayures du quotidien tout en gardant des couleurs éclatantes.\r\n\r\nDouble Attache Pratique : Équipé d\'un anneau robuste en métal argenté ou doré et d\'un mousqueton facile à clipser, il s\'adapte à tous vos supports en un clin d\'œil.\r\n\r\nFinitions Premium : Les détails sont soignés, avec des textures agréables au toucher et souvent accompagnés d\'une petite clochette ou d\'un ruban en tissu logoté.\r\n\r\nPourquoi craquer pour ce porte-clés ?\r\n\r\nPersonnalisation Totale : C\'est le moyen idéal de donner du caractère à un sac souvent trop sobre et de reconnaître vos affaires au premier coup d\'œil.\r\n\r\nLe Cadeau Idéal : Petit, abordable et stylé, c\'est l\'attention parfaite à offrir à un ami fan de K-Pop ou pour se faire plaisir à petit prix.\r\n\r\nCollectionnable : Avec ses multiples déclinaisons (couleurs pastels, néons, thèmes saisonniers), vous pouvez les collectionner et les changer selon votre humeur ou votre \"outfit\" du jour.', 4.90, 48, NULL, 'keychain.jpg', 1, '2026-01-12 11:30:11'),
(11, 2, 'Honey Butter Chips', 'le snack qui a provoqué une véritable \"tempête\" de gourmandise en Corée du Sud et qui reste aujourd\'hui un incontournable pour les amateurs de saveurs sucrées-salées :\r\n\r\nHoney Butter Chips – L\'Équilibre Parfait Sucré-Salé\r\nLes Honey Butter Chips (Habeo) sont bien plus que de simples chips : elles sont à l\'origine d\'un phénomène culturel sans précédent, surnommé la \"Honey Butter Craze\". Connues pour leur saveur addictive et leur finesse exceptionnelle, ces chips offrent une expérience gustative complexe qui bouscule les codes de l\'apéritif traditionnel.\r\n\r\nUne explosion de saveurs en bouche :\r\n\r\nLe Beurre Gastronomique : Fabriquées avec du vrai beurre français de haute qualité, elles offrent une richesse crémeuse et onctueuse qui fond sur la langue.\r\n\r\nLe Miel d\'Acacia : Une touche de miel naturel apporte une douceur florale subtile, créant un contraste fascinant avec le côté salé de la pomme de terre.\r\n\r\nLe Croquant Ultra-Fin : Les pommes de terre sont tranchées très finement et dorées juste ce qu\'il faut pour obtenir une texture légère et croustillante qui ne pèse pas.\r\n\r\nPourquoi sont-elles si célèbres ?\r\n\r\nLe Goût Unique : Là où la plupart des chips sont simplement salées ou épicées, les Honey Butter Chips jouent sur la dualité \"Sweet & Salty\", une tendance très forte en Corée (le fameux Dan-Jjan).\r\n\r\nUne Texture Premium : On sent immédiatement la différence de qualité dès la première bouchée ; elles ne sont ni trop grasses, ni trop sèches.\r\n\r\nLe Snack des Idoles : De nombreuses célébrités de la K-Pop ont partagé leur amour pour ces chips sur les réseaux sociaux, les rendant iconiques dans le monde entier.\r\n\r\nL\'instant dégustation : Elles se marient à merveille avec une boisson fraîche (comme un Milkis ou un thé glacé) pour équilibrer leur richesse beurrée. C\'est le snack idéal pour une soirée film ou une pause gourmande qui sort de l\'ordinaire.', 4.50, 18, NULL, 'honeybutterchips.jpg', 1, '2026-01-12 17:40:22'),
(13, 2, 'Pepero Almond', 'Le Pepero Almond est la déclinaison gourmande par excellence du célèbre biscuit coréen. Alors que la version originale mise sur la finesse, cette édition s\'adresse à ceux qui recherchent une expérience plus intense et généreuse en bouche.\r\n\r\nLes secrets de sa recette :\r\n\r\nAmandes Torréfiées : Le bâtonnet de biscuit est recouvert d\'une multitude d\'éclats d\'amandes grillées de haute qualité. Elles apportent un goût de noisette profond et un craquant rustique.\r\n\r\nEnrobage Chocolat Épais : Pour maintenir les amandes, cette version bénéficie d\'une couche de chocolat au lait plus généreuse et fondante, créant un contraste parfait avec le biscuit sec.\r\n\r\nLe Biscuit Signature : Au cœur, on retrouve le bâtonnet de biscuit \"pretzel\" typique de Lotte, doré au four pour une légèreté constante.\r\n\r\nPourquoi est-ce le préféré des fans ?\r\n\r\nLe Contraste Salé-Sucré : La légère pointe de sel naturelle des amandes vient équilibrer la douceur du chocolat au lait, évitant que le snack ne soit trop sucré.\r\n\r\nUne Texture Multi-Couches : À chaque bouchée, vous ressentez trois sensations : le croquant des amandes, le fondant du chocolat et le craquant du biscuit central.\r\n\r\nLe Snack Énergie : Grâce aux amandes, c\'est une collation qui donne une impression de satiété plus immédiate, idéale pour une pause café ou un goûter énergétique.\r\n\r\nLe conseil de dégustation : C\'est le compagnon idéal d\'un grand verre de lait ou d\'un café noir. En Corée, c\'est souvent la boîte la plus offerte lors du \"Pepero Day\" pour témoigner d\'une attention particulière.', 2.60, 0, '2026-02-01', 'pepero-almond.jpg', 1, '2026-01-12 17:40:22'),
(14, 2, 'Tteokbokki Cup', 'Le Tteokbokki en cup est la version nomade du plat le plus populaire des marchés de Séoul. Il permet de savourer ces célèbres gâteaux de riz moelleux dans une sauce onctueuse, sans avoir à cuisiner pendant des heures. C\'est le \"soul food\" (plat réconfortant) préféré des étudiants et des amateurs de sensations fortes.\r\n\r\nCe qui se cache à l\'intérieur de la cup :\r\n\r\nLes Gâteaux de Riz (Tteok) : De petits bâtonnets cylindriques faits de farine de riz compressée. Une fois réhydratés, ils deviennent incroyablement tendres et élastiques (la fameuse texture chewy).\r\n\r\nLa Sauce Gochujang : Une base de pâte de piment fermenté, à la fois sucrée, salée et plus ou moins pimentée selon les versions. Elle nappe parfaitement les gâteaux de riz pour une explosion de saveurs.\r\n\r\nGarnitures Déshydratées : Selon les marques, on y trouve souvent de petits morceaux d\'oignons verts, de sésame ou parfois des flocons de poisson (fishcake).\r\n\r\nPourquoi on l\'adore :\r\n\r\nUltra-Pratique : Il suffit d\'ajouter un peu d\'eau et de passer la cup au micro-ondes pendant 2 à 3 minutes. C\'est la solution idéale pour un déjeuner rapide au bureau ou un en-cas nocturne.\r\n\r\nVariété de Saveurs : Il existe de nombreuses versions : Original (piquant et sucré), Rose (crémeux avec du lait), Fromage (pour adoucir le piment) ou encore Jjajang (sauce aux haricots noirs).\r\n\r\nTexture Unique : Contrairement aux nouilles instantanées, le Tteokbokki offre une mâche consistante et satisfaisante qui change des snacks habituels.\r\n\r\nL\'astuce du chef : Pour une expérience encore plus gourmande, ajoutez une tranche de fromage cheddar par-dessus juste après la cuisson et mélangez bien. Le fromage fondant rendra la sauce incroyablement crémeuse !', 5.20, 12, NULL, 'tteokbokki-cup.jpg', 1, '2026-01-12 17:40:22'),
(15, 2, 'Buldak Snack', 'Le Buldak Snack est la réponse parfaite pour ceux qui aiment le goût iconique du \"Hot Chicken\" mais préfèrent une texture de gâteau apéritif. Contrairement aux nouilles qu\'il faut cuire, ce snack est prêt à être dévoré dès l\'ouverture du sachet pour un moment de pur plaisir piquant.\r\n\r\nLes caractéristiques du produit :\r\n\r\nTexture Ultra-Crispy : Ce sont de petits morceaux de nouilles de blé pré-frites et pressées, offrant un croquant très satisfaisant sous la dent.\r\n\r\nLe Goût Authentique Buldak : On y retrouve l\'assaisonnement légendaire à base de piment, de poulet grillé, de soja et d\'une pointe de sucre qui crée cette addiction immédiate.\r\n\r\nFormat Pratique : Pas besoin d\'eau bouillante ni de micro-ondes. C\'est le snack idéal à emporter partout (école, travail, voyage).\r\n\r\nNiveau de Piment Équilibré : Bien qu\'il reprenne le parfum des nouilles, le format snack est souvent légèrement moins \"volcanique\" que la version ramen classique, permettant d\'apprécier les saveurs sans trop de souffrance.\r\n\r\nPourquoi les fans en raffolent ?\r\n\r\nL\'effet \"Zéro Préparation\" : C\'est la solution rapide pour satisfaire une envie de Buldak en quelques secondes.\r\n\r\nLe Mélange des Sensations : Le côté gras et craquant de la nouille frite absorbe parfaitement la poudre de piment, créant une explosion de saveurs fumées et épicées.\r\n\r\nParfait pour l\'Apéritif : Il se marie extrêmement bien avec une boisson fraîche pour apaiser le feu du piment.\r\n\r\nConseil de dégustation : Si vous trouvez le snack trop piquant, essayez de le déguster avec une sauce dip à base de mayonnaise ou de crème fraîche pour adoucir le piment tout en conservant le croquant.', 3.40, 0, '2026-01-25', 'buldak-snack.jpg', 1, '2026-01-12 17:40:22'),
(16, 1, 'Ramen Kimchi', 'Voici la description détaillée du Kimchi Ramen, le grand classique de la gastronomie coréenne instantanée qui allie le réconfort des nouilles à la saveur fermentée et piquante du plat national coréen :\r\n\r\nKimchi Ramen – L\'Authenticité Coréenne en Bol\r\nLe Kimchi Ramen est l\'option idéale pour ceux qui recherchent une saveur plus complexe qu\'un simple bouillon épicé. Contrairement aux versions uniquement basées sur le piment, ce ramen capture l\'essence du Kimchi (chou fermenté), offrant une profondeur de goût à la fois acide, salée et épicée.\r\n\r\nCe qui rend ce ramen unique :\r\n\r\nBouillon Acidulé et Relevé : Le bouillon imite parfaitement le jus du kimchi fermenté. C\'est un mélange savoureux de piment rouge, d\'ail, de gingembre et de cette petite pointe d\'acidité caractéristique de la fermentation.\r\n\r\nLégumes Déshydratés : Le sachet d\'accompagnement contient généralement de véritables morceaux de chou napa séché (kimchi), de la ciboule et parfois des carottes, qui reprennent vie et texture au contact de l\'eau bouillante.\r\n\r\nNouilles Fermes et Élastiques : Fidèles à la tradition coréenne, les nouilles sont épaisses et conçues pour absorber le bouillon sans devenir trop molles.\r\n\r\nPourquoi c\'est un incontournable ?\r\n\r\nSaveur Équilibrée : L\'acidité du kimchi coupe le gras des nouilles frites, ce qui rend le plat plus digeste et rafraîchissant que d\'autres types de ramens.\r\n\r\nLe Goût de la Tradition : Pour les Coréens, c\'est le goût de la maison. C\'est le ramen que l\'on choisit quand on veut un repas rapide mais riche en saveurs \"terroir\".\r\n\r\nPolyvalence : Il est excellent tel quel, mais se prête magnifiquement à l\'ajout d\'ingrédients frais (une tranche de fromage, un œuf poché ou même du vrai kimchi frais pour renforcer le goût).\r\n\r\nL\'astuce pour une dégustation parfaite : Laissez infuser les légumes séchés une minute de plus que les nouilles pour qu\'ils retrouvent tout leur croquant. Si vous aimez les soupes épaisses, ajoutez un peu de tofu soyeux à la fin de la cuisson !', 2.10, 35, NULL, 'ramen-kimchi.jpg', 1, '2026-01-12 17:40:22'),
(17, 1, 'Ramen Spicy Seafood', 'Le Spicy Seafood Ramen est une interprétation instantanée du célèbre plat sino-coréen, le Jjamppong. C\'est un ramen qui ne se contente pas d\'être piquant : il offre une profondeur de goût complexe grâce à un bouillon riche en extraits de fruits de mer, évoquant une soupe de pêcheur préparée au coin du feu.\r\n\r\nCe qui compose ce sachet :\r\n\r\nBouillon \"Umami\" Intense : La base de la soupe est infusée avec des extraits de moules, de calamar, de crevettes et d\'algues. Cela donne un goût salin et profond qui équilibre parfaitement la chaleur du piment.\r\n\r\nGarnitures de l\'Océan : Le sachet de légumes contient souvent de généreux morceaux de wakame (algues), de petits flocons de poisson ou de calamar déshydraté, et parfois des champignons pour ajouter de la mâche.\r\n\r\nHuile Aromatique (parfois incluse) : Certaines versions premium incluent un petit sachet d\'huile de piment fumée qui apporte ce goût de \"wok\" (le fameux Bul-mat) typique des restaurants de Jjamppong.\r\n\r\nPourquoi les amateurs de fruits de mer l\'adorent ?\r\n\r\nUn Piquant Rafraîchissant : Contrairement au Buldak qui est \"sec\" et brûlant, le Spicy Seafood offre une chaleur liquide qui dégage les sinus tout en étant très désaltérante.\r\n\r\nLe Contraste Terre-Mer : Le mélange entre le piment rouge coréen (Gochugaru) et le goût sucré-salé des crustacés crée une harmonie unique en bouche.\r\n\r\nTexture des Nouilles : Les nouilles sont généralement plus épaisses pour supporter la puissance du bouillon et rappeler les pâtes fraîches faites à la main.\r\n\r\nLe conseil pour une version \"Luxe\" : Pendant que les nouilles cuisent, ajoutez quelques crevettes décortiquées congelées ou des bâtonnets de surimi. Terminez avec un filet de jus de citron vert pour couper le gras et faire ressortir le goût du calamar', 2.30, 16, NULL, 'ramen-seafood.jpg', 1, '2026-01-12 17:40:22'),
(18, 1, 'Buldak Carbonara', 'Le Buldak Carbonara est devenu un véritable phénomène culturel. Il a été créé pour célébrer les un milliard de ventes de la gamme Buldak. Sa force réside dans son équilibre parfait : il conserve le piquant légendaire du poulet de feu coréen, mais l\'adoucit avec une poudre crémeuse inspirée de la cuisine italienne.\r\n\r\nLes composants clés du paquet :\r\n\r\nLes Nouilles Larges : Contrairement aux versions classiques, les nouilles du Carbonara sont plus plates et larges (type linguine). Cette forme permet à la sauce épaisse de mieux adhérer à la pâte.\r\n\r\nLa Sauce Buldak (Sachet Liquide) : La base pimentée originale, fumée et intense.\r\n\r\nLa Poudre de Carbo (Sachet Rose) : Un mélange riche de poudre de lait, de fromage (mozzarella/parmesan), d\'ail et de persil. C’est elle qui apporte la texture veloutée et le goût crémeux.\r\n\r\nPourquoi est-ce un best-seller mondial ?\r\n\r\nUn Piquant Maîtrisé : C\'est l\'une des versions les moins agressives de la gamme (environ 2 600 SHU sur l\'échelle de Scoville, contre 4 400 pour l\'original). La crème neutralise une partie du feu, le rendant accessible à un plus large public.\r\n\r\nLe Mix \"K-Rosé\" : Il surfe sur la tendance \"Rosé\" en Corée, qui consiste à mélanger des sauces piquantes avec de la crème pour un résultat gourmand et addictif.\r\n\r\nUne Texture Onctueuse : Une fois mélangé, le bouillon se transforme en une sauce riche et nappante qui rappelle un plat de pâtes de restaurant.\r\n\r\nConseil pour une préparation \"TikTok style\" : Ne videz pas toute l\'eau de cuisson (gardez environ 8 cuillères à soupe). Ajoutez un filet de lait entier et une tranche de fromage à la fin pour rendre la sauce encore plus crémeuse et \"cheesy\".', 3.20, 10, NULL, 'buldak-carbonara.jpg', 1, '2026-01-12 17:40:22'),
(19, 1, 'Buldak 2x Spicy', 'Le Buldak 2x Spicy (souvent appelé \"Nuclear Edition\") est la version intensifiée du ramen piquant original. Connu mondialement pour avoir lancé le \"Fire Noodle Challenge\" sur les réseaux sociaux, ce paquet rouge vif est un avertissement en soi : il n\'est pas destiné aux cœurs fragiles.\r\n\r\nLes caractéristiques extrêmes du produit :\r\n\r\nLe Niveau de Piment (Scoville) : Avec environ 8 800 SHU, il est deux fois plus piquant que l\'original. C\'est une chaleur qui monte instantanément et qui persiste longuement en bouche.\r\n\r\nLa Sauce Noire et Rouge : Une sauce épaisse, presque sombre, qui contient une concentration massive d\'extraits de piment, d\'huile de soja, d\'ail et d\'oignon grillé pour une saveur fumée.\r\n\r\nLes Nouilles \"Chewy\" : Des nouilles de blé épaisses et élastiques, spécialement conçues pour être sautées et non servies en soupe, afin que la sauce s\'y accroche au maximum.\r\n\r\nLa Garniture : Un petit sachet de graines de sésame grillées et de lamelles d\'algues séchées (nori) pour apporter une touche craquante et umami.\r\n\r\nPourquoi est-il devenu une légende ?\r\n\r\nLe Défi Ultime : Il est devenu le symbole du courage culinaire. Réussir à finir son bol sans boire de lait est considéré comme un véritable exploit par les fans.\r\n\r\nUn Goût Addictif : Malgré la douleur, il possède ce goût de poulet grillé et de sauce soja légèrement sucrée qui rend chaque bouchée (douloureusement) savoureuse.\r\n\r\nDesign Iconique : Le packaging met en scène Hochi, le poulet mascotte, qui crache littéralement des flammes, symbolisant parfaitement l\'expérience qui vous attend.\r\n\r\nConseils de survie :\r\n\r\nNe videz pas toute l\'eau : Gardez un peu d\'eau de cuisson pour aider la sauce à bien napper les nouilles sans qu\'elles ne collent.\r\n\r\nPréparez des secours : Gardez un verre de lait entier, de la crème fraîche ou une tranche de fromage à portée de main pour neutraliser la capsaïcine.\r\n\r\nNe buvez pas d\'eau gazeuse : Cela ne ferait qu\'accentuer la sensation de brûlure !', 3.30, 0, '2026-02-10', 'buldak-2x.jpg', 1, '2026-01-12 17:40:22'),
(20, 1, 'Ramen Jjajang', 'Le Jjajang Ramen est l\'adaptation instantanée du Jajangmyeon, un plat sino-coréen historique. Contrairement aux ramens rouges et brûlants, celui-ci mise sur la profondeur, le côté terreux et une légère note sucrée qui plaît à toutes les générations.\r\n\r\nCe que vous trouverez dans le sachet :\r\n\r\nLa Sauce aux Haricots Noirs (Chunjang) : Le cœur du plat. C\'est une pâte fermentée de soja et de farine de blé, torréfiée avec du caramel pour donner cette couleur ébène et ce goût unique de noisette grillée.\r\n\r\nNouilles Épaisses et Moelleuses : Pour imiter les pâtes fraîches tirées à la main, les nouilles du Jjajang sont généralement plus dodues et tendres que celles des ramens classiques.\r\n\r\nGarniture Texturée : Le mélange déshydraté contient souvent des morceaux de soja (qui imitent la viande), des oignons, des carottes et parfois des petits pois, qui sont essentiels pour apporter du croquant.\r\n\r\nHuile de Cuisson : Un petit sachet d\'huile (souvent à l\'oignon ou végétale) est inclus pour donner cet aspect brillant et soyeux à la sauce finale.\r\n\r\nPourquoi est-il si spécial ?\r\n\r\nZéro Piquant (ou presque) : C\'est l\'alternative parfaite pour ceux qui veulent savourer des nouilles coréennes sans souffrir de la chaleur du piment.\r\n\r\nLe Goût \"Umami\" : La fermentation des haricots noirs crée une explosion de saveurs savoureuses et réconfortantes, très différente des soupes à base de bouillon.\r\n\r\nUn Plat de Célébration : En Corée, le Jajangmyeon est traditionnellement mangé lors du \"Black Day\" (pour les célibataires) ou pour fêter un déménagement.\r\n\r\nLa méthode \"Chapaguri\" (Parasite) : Si vous voulez pimenter votre Jjajang, faites comme dans le film Parasite : mélangez un paquet de Jjajang avec un paquet de Neoguri (fruits de mer épicés). C\'est le mélange sucré-salé-épicé ultime !', 2.80, 22, NULL, 'ramen-jjajang.jpg', 1, '2026-01-12 17:40:22'),
(21, 1, 'Ramen Udon', 'Le Ramen Udon en version instantanée est conçu pour ceux qui recherchent une expérience plus consistante. Contrairement aux ramens classiques, l\'accent est mis ici sur la texture unique des nouilles, inspirée des traditions japonaises et coréennes.\r\n\r\nLes éléments distinctifs du paquet :\r\n\r\nNouilles Ultra-Épaisses : C\'est la signature de l\'Udon. Les nouilles sont beaucoup plus larges et denses, offrant une texture \"chewy\" (élastique) très satisfaisante sous la dent.\r\n\r\nBouillon de Dashi et Soja : Le bouillon est généralement clair et savoureux, à base de bonite séchée (poisson), d\'algues et de sauce soja. C\'est un goût pur, boisé et moins gras que les autres soupes.\r\n\r\nLa Feuille de Kombu : Dans les versions premium (comme le Neoguri), vous trouverez une véritable feuille d\'algue séchée entière à infuser, ce qui apporte une richesse minérale naturelle.\r\n\r\nGarnitures de Tempura ou Tofu : Selon la variante, le sachet peut contenir des petits morceaux de tempura croustillants ou des dés de tofu frit (Abura-age) qui absorbent le bouillon.\r\n\r\nPourquoi est-ce un choix incontournable ?\r\n\r\nRéconfortant et Doux : C\'est le ramen idéal pour les jours de pluie ou de froid. Sa chaleur est enveloppante et son niveau de piment est généralement très bas (voire inexistant pour les versions classiques).\r\n\r\nUne Sensation de Satiété : Grâce à l\'épaisseur de ses nouilles, un seul bol d\'Udon est souvent bien plus rassasiant qu\'un ramen standard.\r\n\r\nÉquilibre des Saveurs : Il offre un profil de goût \"Umami\" très propre, sans l\'agressivité des sauces pimentées, mettant en avant la qualité des ingrédients marins.\r\n\r\nLe conseil du chef : Ajoutez un œuf poché directement dans le bouillon chaud et quelques oignons verts ciselés. Pour une touche authentique, une petite pincée de Shichimi Togarashi (mélange de 7 épices japonais) viendra relever le tout sans masquer le goût délicat de l\'Udon.', 2.70, 14, NULL, 'ramen-udon.jpg', 1, '2026-01-12 17:40:22'),
(22, 3, 'Soju (sans alcool) - Pêche', 'Cette version moderne du célèbre spiritueux coréen remplace l\'éthanol par une base d\'eau pétillante légère ou d\'eau purifiée, tout en conservant le profil aromatique iconique qui a rendu le Soju aux fruits mondialement célèbre.\r\n\r\nCe qui définit cette boisson :\r\n\r\nL\'Arôme Naturel de Pêche : Dès l\'ouverture, on sent une odeur gourmande de pêche blanche juteuse. Le goût est doux, velouté et très rafraîchissant, sans l\'amertume de l\'alcool.\r\n\r\nLa Bouteille Iconique : Elle conserve la forme traditionnelle en verre vert émeraude, mais avec une étiquette souvent pastel (rose ou orange) pour indiquer la saveur pêche et la mention \"0.0% Alcohol\".\r\n\r\nTexture Cristalline : La boisson est parfaitement transparente. Elle peut être légèrement pétillante pour apporter du peps ou plate pour imiter la texture soyeuse du soju classique.\r\n\r\nFaible en Calories : Sans le sucre lourd associé aux cocktails alcoolisés, cette version est souvent une alternative plus saine tout en restant très festive.\r\n\r\nPourquoi l\'adopter ?\r\n\r\nLe Mariage Parfait avec le Piment : C\'est le compagnon idéal pour vos Buldak 2x Spicy. Le sucre naturel de la pêche aide à calmer instantanément le feu du piment sur votre langue.\r\n\r\nConvivialité sans Compromis : Vous pouvez participer aux rituels de \"cheers\" (Geonbae !) avec vos amis sans ressentir les effets de l\'alcool.\r\n\r\nPolyvalence : Il se déguste très frais dans de petits verres à shot traditionnels, ou peut servir de base pour un mocktail plus élaboré avec des morceaux de fruits frais et de la menthe.\r\n\r\nL\'astuce de dégustation : Placez la bouteille au congélateur pendant 15 minutes avant de servir pour obtenir une texture presque givrée. C’est ainsi que les saveurs de pêche s’expriment le mieux !', 3.50, 29, NULL, 'soju-peach.jpg', 1, '2026-01-12 17:40:22'),
(23, 3, 'Soju (sans alcool) - Raisin', 'Le Soju au Raisin Vert (Green Grape) est une icône de la culture \"K-drink\". Cette version sans alcool capture l\'essence même du fruit : un mélange parfait entre la sucrosité du raisin mûr et une pointe d\'acidité qui nettoie le palais.\r\n\r\nLes caractéristiques de cette boisson :\r\n\r\nProfil Aromatique : Une saveur intense de raisin blanc de type muscat. C\'est un goût frais, croquant et légèrement floral qui explose en bouche.\r\n\r\nL\'Aspect Visuel : Présenté dans sa bouteille verte traditionnelle, l\'étiquette arbore des grappes de raisins verts éclatants. Le liquide lui-même reste parfaitement limpide et cristallin.\r\n\r\nSensation en Bouche : Moins sirupeux que la pêche, le raisin vert offre une finition plus nette et vive, ce qui le rend particulièrement désaltérant.\r\n\r\nZéro Alcool, Maximum de Plaisir : Élaboré pour reproduire la fluidité du soju original, il permet de profiter de l\'expérience sociale coréenne sans les effets de l\'éthanol.\r\n\r\nPourquoi est-ce le meilleur choix pour accompagner vos plats ?\r\n\r\nLe \"Cleaner\" de Palais : Grâce à son acidité naturelle, le Soju Raisin Vert est idéal après une bouchée de Jjajang Ramen (plus gras) ou de Ramen Udon. Il \"nettoie\" les papilles pour la bouchée suivante.\r\n\r\nMoins de Sucre Ressenti : Bien que fruité, son côté acidulé donne une impression de légèreté supérieure aux autres parfums comme la fraise ou la prune.\r\n\r\nCocktail Facile : Mélangez-le avec un peu de limonade ou d\'eau gazeuse et des grains de raisin congelés pour créer un mocktail chic et ultra-frais en quelques secondes.\r\n\r\nLe conseil de dégustation : À boire très glacé dans un verre à shot. En Corée, il est de coutume de dire que le goût du raisin vert est celui qui se rapproche le plus d\'un bonbon fruité, ce qui en fait la boisson préférée pour accompagner les repas conviviaux.', 3.50, 0, '2026-01-20', 'soju-grape.jpg', 1, '2026-01-12 17:40:22'),
(24, 3, 'Aloe Vera Drink', 'Plongez dans une expérience de désaltération pure avec cette boisson à l\'aloe vera. Alliant les bienfaits d\'une plante millénaire à un plaisir gustatif moderne, c\'est l\'alternative parfaite aux sodas trop sucrés pour ceux qui recherchent une boisson à la fois saine et gourmande.\r\n\r\nCe qui la rend unique :\r\n\r\nVrais Morceaux de Pulpe : Contrairement aux boissons classiques, celle-ci contient des cubes de gel d\'aloe vera frais. Ils apportent une texture croquante et ludique qui rend chaque gorgée unique.\r\n\r\nHydratation Maximale : Naturellement riche en eau et en nutriments, l\'aloe vera aide à revitaliser le corps et à hydrater la peau de l\'intérieur.\r\n\r\nSaveur Douce et Florale : Un goût délicat, légèrement sucré avec une pointe de miel ou de raisin, qui laisse une sensation de propreté et de fraîcheur en bouche.\r\n\r\nBienfaits Digestifs : Reconnue pour ses propriétés apaisantes sur le système digestif, c\'est la boisson idéale après un repas épicé (comme des Ramen Buldak !).\r\n\r\nPourquoi vous allez l\'adopter :\r\n\r\nSans Colorants Artificiels : Une robe translucide qui reflète la pureté des ingrédients naturels.\r\n\r\nBouteille Ergonomique : Son format iconique est facile à tenir et parfait pour vous accompagner à la salle de sport, au bureau ou en balade.\r\n\r\nServir Très Frais : C\'est glacée qu\'elle révèle tout son potentiel rafraîchissant.\r\n\r\nConseil dégustation : Agitez légèrement la bouteille avant de l\'ouvrir pour bien répartir les morceaux de pulpe d\'aloe vera et profiter de toutes les textures dès la première gorgé', 2.90, 20, NULL, 'aloe2.jpg', 1, '2026-01-12 17:40:22'),
(25, 3, 'Milkis', 'Découvrez le Milkis, une boisson gazeuse coréenne emblématique qui défie les conventions des sodas classiques. Son goût unique et rafraîchissant, à la fois crémeux et pétillant, en a fait une favorite en Corée et bien au-delà. C\'est le mélange parfait entre une limonade légère et une touche de lait pour une expérience gustative inattendue.\r\n\r\nCe qui le rend si unique :\r\n\r\nL\'Alliance Surprenante : Le Milkis combine la légèreté d\'un soda pétillant avec une subtile onctuosité lactée, rappelant parfois une crème glacée fondue ou un yaourt à boire gazéifié.\r\n\r\nDouceur Équilibrée : Sa saveur légèrement sucrée et son arrière-goût doux le rendent extrêmement désaltérant sans être écœurant.\r\n\r\nGoût \"Soft Drink\" Reconfortant : C\'est une boisson qui évoque la nostalgie et la simplicité, souvent appréciée pour son côté réconfortant.\r\n\r\nSans Colorants Artificiels : Sa couleur blanche laiteuse est naturelle, provenant du lait ou du lait écrémé en poudre qu\'elle contient.\r\n\r\nPourquoi l\'essayer ?\r\n\r\nExpérience Originale : Si vous aimez les nouvelles saveurs et les boissons uniques, le Milkis est un incontournable.\r\n\r\nIdéal pour le Goûter : Il accompagne parfaitement les snacks salés ou sucrés et est très apprécié des enfants comme des adultes.\r\n\r\nServir Très Frais : C\'est glacé que le Milkis révèle toute sa puissance rafraîchissante et que son mélange crémeux-pétillant est le plus agréable.', 2.40, 26, NULL, 'milkis2.jpg', 1, '2026-01-12 17:40:22'),
(26, 3, 'Yuzu Tea (concentré)', 'Découvrez le Yuja-cha, bien plus qu\'un simple thé : c\'est une délicieuse marmelade de yuzu infusée au miel, traditionnellement utilisée en Corée pour booster l\'immunité et apaiser l\'esprit. Ce concentré capture l\'arôme unique et puissant du yuzu de l\'île de Jeju.\r\n\r\nLes points forts de ce concentré :\r\n\r\nVéritables Morceaux de Fruit : Contient de fines lamelles de zestes et de pulpe de yuzu frais, offrant une texture gourmande et une explosion de saveurs acidulées à chaque cuillère.\r\n\r\nÉquilibre Parfait : L\'acidité vive et caractéristique du yuzu (entre le citron et la mandarine) est délicatement balancée par la douceur du miel de fleurs, créant une harmonie sucrée-salée unique.\r\n\r\nAllié Santé Naturel : Naturellement riche en Vitamine C (trois fois plus que le citron classique), il est le remède coréen favori pour combattre la fatigue et soulager les maux de gorge.\r\n\r\nPolyvalence Totale : Ce concentré n\'est pas réservé qu\'au thé ! Utilisez-le en infusion chaude, en boisson glacée rafraîchissante, en nappage sur un yaourt ou même comme base de vinaigrette originale.\r\n\r\nMode de préparation : Mélangez simplement deux à trois cuillères à café de ce concentré dans une tasse d\'eau chaude (ou d\'eau gazeuse pour une version limonade) et remuez. Savourez instantanément l\'éclat du soleil coréen.', 7.90, 8, NULL, 'yuzu-tea.jpg', 1, '2026-01-12 17:40:22'),
(27, 4, 'Masque tissu - Hydratation', 'Véritable cure de jeunesse pour l\'épiderme, ce masque en tissu imprégné de sérum hautement concentré agit comme un réservoir d\'eau pour votre peau. Il est l\'allié parfait pour combattre les signes de déshydratation, les ridules et les tiraillements.\r\n\r\nLes ingrédients clés :\r\n\r\nAcide Hyaluronique Multi-Moléculaire : Capable de retenir jusqu\'à 1000 fois son poids en eau, il hydrate simultanément en surface et dans les couches plus profondes de la peau.\r\n\r\nExtrait de Bambou & Aloe Vera : Des actifs naturels coréens connus pour leurs vertus apaisantes et leur capacité à régénérer la barrière cutanée.\r\n\r\nCéramides : Pour sceller l\'hydratation à l\'intérieur de la peau et renforcer sa protection naturelle contre les agressions extérieures.\r\n\r\nPourquoi vous allez l\'adorer :\r\n\r\nTechnologie \"Aqua-Lock\" : Le tissu en coton naturel retient le sérum et assure une diffusion lente et continue, garantissant que votre peau absorbe chaque goutte d\'actif.\r\n\r\nEffet Repulpant Immédiat : En seulement 15 minutes, la peau est visiblement plus lisse, les zones de sécheresse disparaissent et le visage retrouve sa souplesse.\r\n\r\nFormule Sans Parfum : Doux et non irritant, il convient parfaitement aux peaux les plus sensibles et réactives.\r\n\r\nConseil Beauté : Pour un effet encore plus décongestionnant et rafraîchissant, placez votre masque au réfrigérateur 10 minutes avant l\'utilisation. C\'est le remède idéal après un voyage en avion ou une exposition au soleil.', 2.20, 60, NULL, 'mask-hydra.jpg', 1, '2026-01-12 17:40:22'),
(28, 4, 'Masque tissu - Éclat', 'Véritable concentré de sérum dans un format pratique, ce masque en tissu est conçu pour réveiller les teints ternes et fatigués en seulement 15 minutes. C\'est le secret des Coréennes pour afficher une peau radieuse avant un événement ou après une longue journée.\r\n\r\nLes ingrédients boosters d\'éclat :\r\n\r\nVitamine C & Extrait de Yuzu : Un puissant antioxydant qui illumine le teint, estompe les taches pigmentaires et protège la peau contre le stress oxydatif.\r\n\r\nNiacinamide (Vitamine B3) : Resserre les pores et améliore la clarté du teint pour un fini lisse et uniforme.\r\n\r\nEau de Perle : Apporte une luminosité naturelle et un fini \"rosé\" immédiat à la peau.\r\n\r\nLes points forts du produit :\r\n\r\nTissu en Microfibre Biodégradable : Une matière ultra-fine qui épouse parfaitement les contours du visage (effet \"seconde peau\"), permettant au sérum de pénétrer en profondeur sans s\'évaporer.\r\n\r\nEffet Frais Immédiat : Calme instantanément les inflammations et décongestionne le visage pour un aspect reposé.\r\n\r\nSérum Généreux : Chaque sachet contient l\'équivalent d\'une demi-bouteille de sérum. Le surplus peut être appliqué sur le cou, le décolleté et les mains.\r\n\r\nConseil d\'utilisation : Appliquez sur peau propre et laissez poser 15 à 20 minutes. Retirez le masque et tapotez doucement l\'excédent de sérum jusqu\'à absorption complète. Ne pas rincer pour laisser les actifs agir toute la journée (ou la nuit).', 2.20, 55, NULL, 'mask-glow.jpg', 1, '2026-01-12 17:40:22'),
(29, 4, 'Nettoyant visage doux', 'Inspiré des secrets de beauté ancestraux des femmes coréennes qui utilisaient l\'eau de riz pour blanchir et adoucir leur peau, ce nettoyant moussant est la base idéale de toute routine K-Beauty. Sa formule à pH équilibré respecte le film hydrolipidique de votre peau tout en éliminant les impuretés accumulées.\r\n\r\nLes bénéfices clés :\r\n\r\nEau de Son de Riz (Oryza Sativa) : Naturellement riche en vitamines et minéraux, elle aide à illuminer le teint et à adoucir la texture de la peau dès le premier lavage.\r\n\r\nNettoyage pH Neutre : Formulé pour être proche du pH naturel de la peau (environ 5.5), ce qui évite les tiraillements et les irritations souvent causés par les nettoyants classiques.\r\n\r\nMousse Onctueuse : Sa texture se transforme en une mousse dense et crémeuse (Micro-Bubble) qui déloge les particules de pollution et l\'excès de sébum jusque dans les pores.\r\n\r\nComplexe Apaisant : Enrichi en Allantoïne et en Panthénol pour calmer les peaux sensibles et renforcer la barrière cutanée.\r\n\r\nMode d\'utilisation : Massez une petite noisette de produit sur visage humide en effectuant des mouvements circulaires, puis rincez à l\'eau tiède. Utilisé matin et soir, il laisse la peau incroyablement douce, souple et prête à recevoir les soins suivants.\r\n\r\nLe détail design : Le flacon-pompe minimaliste, au fini givré, reflète la pureté et la clarté que ce soin apporte à votre visage.', 11.90, 15, NULL, 'cleanser.jpg', 1, '2026-01-12 17:40:22'),
(30, 4, 'Crème hydratante', 'Découvrez l\'excellence des soins coréens avec cette crème hydratante haute performance. Conçue pour offrir une hydratation profonde tout en restant incroyablement légère, elle est la clé pour obtenir un teint frais, rebondi et lumineux, emblématique de la beauté à la coréenne.\r\n\r\nLes ingrédients stars :\r\n\r\nMucine d\'Escargot & Centella Asiatica : Un duo puissant pour apaiser les rougeurs, réparer la barrière cutanée et lisser le grain de peau.\r\n\r\nAcide Hyaluronique Multi-poids : Pénètre les différentes couches de l\'épiderme pour une hydratation longue durée (72h) et un effet repulpant immédiat.\r\n\r\nExtrait de Riz & Niacinamide : Des ingrédients traditionnels utilisés pour illuminer le teint, réduire l\'apparence des pores et uniformiser la peau.\r\n\r\nTexture et Application :\r\n\r\nTexture \"Sorbet\" : Une formule gel-crème innovante qui fond instantanément au contact de la peau sans laisser de film gras ou collant.\r\n\r\nAbsorption Rapide : Parfaite comme base de maquillage ou comme dernière étape de votre routine du soir pour une peau régénérée au réveil.\r\n\r\nParfum Subtil : Une fragrance légère et apaisante aux notes de thé vert et de fleurs de lotus.\r\n\r\nEngagement Qualité : Présentée dans un pot en verre minimaliste et éco-conçu, cette crème est formulée sans parabènes, sans sulfates et est adaptée aux peaux les plus sensibles.', 18.90, 8, NULL, 'moisturizer.jpg', 1, '2026-01-12 17:40:22'),
(31, 4, 'Sérum Niacinamide', 'Ce Sérum Niacinamide 10% + Zinc 1% est un puissant soin hydratant et équilibrant conçu pour améliorer la texture de la peau et réduire l\'apparence des imperfections. Sa formule légère et concentrée aide à minimiser les pores dilatés, à réguler la production de sébum et à uniformiser le teint. Enrichi en zinc, il apaise les irritations et favorise une peau plus nette et plus saine. Idéal pour tous les types de peau, en particulier les peaux sujettes aux imperfections et aux brillances.', 14.90, 0, '2026-02-05', 'serum-niacinamide.jpg', 1, '2026-01-12 17:40:22'),
(32, 5, 'Album K-Pop (version A)', 'Cet \"Album K-Pop (Version A) - Édition Collector Démo\" est une pièce de collection exclusive pour les fans de K-Pop. Le coffret, d\'un design moderne et élégant, arbore des motifs holographiques irisés qui changent de couleur sous la lumière, créant un effet visuel dynamique.\r\n\r\nÀ l\'intérieur, vous trouverez :\r\n\r\nUn Photobook grand format : Richement illustré avec des clichés exclusifs des membres du groupe, capturant leurs charmes et personnalités uniques.\r\n\r\nUn CD audio : Contenant les titres démo de l\'album, avec un design minimaliste et des reflets arc-en-ciel.\r\n\r\nDes Photocards de collection : Un ensemble de cartes photo brillantes à collectionner, mettant en vedette les artistes dans diverses poses et tenues.', 24.90, 7, NULL, 'album-a.jpg', 1, '2026-01-12 17:40:22');
INSERT INTO `items` (`id`, `category_id`, `name`, `description`, `price`, `stock`, `restock_at`, `image`, `is_active`, `published_at`) VALUES
(33, 5, 'Album K-Pop (version B)', 'Cet \"Album K-Pop (Version B) - Édition Rebel Concept\" est une pièce de collection exclusive pour les fans de K-Pop. Le coffret, d\'un design moderne et élégant.\r\nÀ l\'intérieur, vous trouverez :\r\n\r\nUn Photobook grand format : Richement illustré avec des clichés exclusifs des membres du groupe, capturant leurs charmes et personnalités uniques.\r\n\r\nUn CD audio : Contenant les titres démo de l\'album, avec un design minimaliste et des reflets arc-en-ciel.\r\n\r\nDes Photocards de collection : Un ensemble de cartes photo brillantes à collectionner, mettant en vedette les artistes dans diverses poses et tenues.', 24.90, 0, '2026-03-01', 'album-b.jpg', 1, '2026-01-12 17:40:22'),
(34, 5, 'Lightstick fan', 'Cet élégant Lightstick Fan est l\'accessoire indispensable pour tout fan de K-Pop souhaitant montrer son soutien avec style. Son design futuriste présente un dôme transparent abritant un motif géométrique lumineux qui émet une douce lueur violette et bleue, créant une ambiance magique lors des concerts et événements.\r\n\r\nLe manche ergonomique et épuré offre une prise en main confortable, tandis qu\'un bouton discret permet de contrôler les différentes modes d\'éclairage. Un logo stylisé \"A\" est subtilement intégré au design, ajoutant une touche d\'identité.\r\n\r\nCe lightstick est bien plus qu\'un simple objet lumineux ; c\'est un symbole de connexion entre les fans et leurs artistes préférés. Sa base est dotée d\'une dragonne pour une sécurité accrue et un port USB pour une recharge facile. Parfaitement mis en valeur sur un fond noir profond avec des étoiles scintillantes, il capture l\'essence de la galaxie K-Pop.', 49.90, 5, NULL, 'lightstick.jpg', 1, '2026-01-12 17:40:22'),
(35, 5, 'Photocard Pack', 'Ce Photocard Pack est l\'objet de collection ultime pour les passionnés de K-Pop, regroupant les clichés les plus rares et les plus esthétiques de vos idoles préférées. Présenté dans un étui au design minimaliste et luxueux sur fond noir, ce pack est conçu pour protéger et sublimer chaque image.\r\n\r\nÀ l\'intérieur de ce pack, vous découvrirez :\r\n\r\nUn ensemble de 10 cartes exclusives : Imprimées sur un papier cartonné haute densité avec une finition mate \"soft-touch\" pour un toucher soyeux et une durabilité accrue.\r\n\r\nDétails Holographiques : Chaque carte possède des bordures ou des détails de signature en dorure holographique qui scintillent à la lumière, garantissant l\'authenticité de l\'édition.\r\n\r\nVerso Personnalisé : Le dos de chaque carte comporte un message manuscrit imprimé ou un logo spécial, rendant chaque exemplaire unique.\r\n\r\nFormat Standard (55x85mm) : Conçu pour s\'adapter parfaitement à vos classeurs de collection, porte-cartes ou à l\'arrière de votre coque de téléphone.', 6.90, 25, NULL, 'photocard.jpg', 1, '2026-01-12 17:40:22'),
(36, 7, 'Porte-clés Hanbok', 'Porte-clés Hanbok Miniature - Édition Artisanale\r\nCe porte-clés exclusif est une pièce de collection délicate qui rend hommage au patrimoine culturel coréen. Représentant un Hanbok (vêtement traditionnel) miniature avec une précision incroyable, cet accessoire est le moyen idéal de personnaliser vos clés, votre sac à dos ou votre pochette d\'album K-Pop.\r\n\r\nDétails et caractéristiques du produit :\r\n\r\nDesign Traditionnel & Moderne : Le mini-hanbok est conçu avec des tissus soyeux de haute qualité. Il présente des motifs floraux brodés et des couleurs vives (souvent un contraste de rose pastel, blanc et bleu) typiques des célébrations royales.\r\n\r\nFinitions de Luxe : L\'ensemble est orné d\'un petit Norigae (pendentif traditionnel décoratif) avec un pompon en fil de soie fin, ajoutant une touche d\'élégance et de mouvement.\r\n\r\nMatériaux Durables : L\'attache est un mousqueton en métal doré robuste, conçu pour résister à une utilisation quotidienne tout en conservant son éclat.\r\n\r\nDétails du Coffret : Présenté sur un fond noir profond pour faire ressortir les couleurs éclatantes du tissu, ce porte-clés est une véritable pièce d\'art miniature.\r\n\r\nPourquoi l\'adopter ? C\'est le cadeau parfait pour les amoureux de la culture coréenne. Compact et léger, il symbolise la grâce et la beauté de la Corée du Sud, tout en restant un accessoire de mode tendance et discret.', 4.90, 30, NULL, 'keychain-hanbok.jpg', 1, '2026-01-12 17:40:22'),
(37, 7, 'Stickers Hangul', 'Planche de Stickers Hangul - Édition Calligraphie Moderne\r\nApportez une esthétique authentique et éducative à vos objets du quotidien avec cette planche de stickers dédiée à l\'alphabet coréen. Conçue avec un mélange de typographies modernes et de calligraphies traditionnelles, cette collection est idéale pour les étudiants en langue, les fans de K-Pop et les amateurs de papeterie créative.\r\n\r\nCe que contient cet ensemble :\r\n\r\nAlphabet Complet : Comprend les consonnes et voyelles de base du Hangul, ainsi que des syllabes stylisées formant des mots positifs (comme \"Amour\", \"Espoir\" ou \"Rêve\").\r\n\r\nDesign Holographique & Mat : Chaque sticker possède un contour délicatement irisé qui reflète la lumière, contrastant avec un fini mat élégant sur le reste de la lettre.\r\n\r\nQualité Premium (Vinyl) : Fabriqués en vinyle de haute qualité, ces autocollants sont résistants à l\'eau, aux rayures et ne laissent aucun résidu collant une fois retirés.\r\n\r\nPolyvalence : Parfaitement dimensionnés pour décorer votre ordinateur portable, votre gourde, votre journal intime ou même votre lightstick.\r\n\r\nLe petit plus : Le design présenté sur fond noir souligne la pureté des lignes du Hangul, transformant chaque caractère en un véritable petit bijou graphique. C\'est un outil ludique et esthétique pour s\'immerger un peu plus dans la culture coréenne.', 3.50, 69, NULL, 'stickers-hangul.jpg', 1, '2026-01-12 17:40:22'),
(38, 7, 'Tote bag Seoul', 'Tote Bag Seoul, un accessoire à la fois pratique et ultra-tendance pour afficher votre passion pour la capitale coréenne :\r\n\r\nTote Bag \"Soul of Seoul\" – Édition Cityscape\r\nCe Tote Bag Seoul est l\'allié idéal de votre quotidien, que ce soit pour transporter vos albums de K-Pop, vos cours ou vos achats lors d\'une virée shopping. Son design minimaliste capture l\'essence vibrante de Séoul, mélangeant modernité architecturale et sérénité nocturne.\r\n\r\nCaractéristiques et finitions :\r\n\r\nDesign Artistique : L\'illustration met en scène la célèbre N Seoul Tower surplombant la skyline de la ville. Le graphisme utilise des tons néons et pastels qui ressortent magnifiquement sur le textile noir profond.\r\n\r\nMatériau Éco-responsable : Fabriqué en coton canvas épais (340g/m²), ce sac est extrêmement résistant et conçu pour durer dans le temps sans se déformer.\r\n\r\nGrand Format Pratique : Ses dimensions généreuses permettent d\'accueillir facilement un ordinateur portable, des magazines ou vos essentiels de concert.\r\n\r\nConfort de portage : Les anses longues et renforcées assurent un maintien confortable sur l\'épaule, même lorsque le sac est bien rempli.\r\n\r\nUn accessoire de mode indispensable : Présenté ici dans une mise en scène moderne sur fond noir, ce sac ne se contente pas d\'être utile : il est un véritable accessoire de mode \"streetwear\" qui complète parfaitement n\'importe quel look inspiré de la mode coréenne.', 12.90, 18, NULL, 'totebag-seoul.jpg', 1, '2026-01-12 17:40:22'),
(39, 7, 'Pin Namsan Tower', 'Pin\'s Émaillé Namsan Tower – Édition \"Nights in Seoul\"\r\nAjoutez une touche de la skyline de Séoul à votre collection avec ce pin\'s de haute qualité. Représentant la célèbre N Seoul Tower illuminée, cet accessoire est conçu pour les voyageurs dans l\'âme et les passionnés de culture coréenne qui souhaitent porter un souvenir de la ville sur eux.\r\n\r\nDétails et finitions du produit :\r\n\r\nÉmail Dur (Hard Enamel) : Réalisé avec une technique d\'émaillage haut de gamme, offrant une surface lisse, plane et extrêmement résistante aux rayures.\r\n\r\nDesign Lumineux : La tour est mise en valeur par des détails aux couleurs dégradées (du violet électrique au bleu néon), rappelant les jeux de lumières nocturnes de la tour.\r\n\r\nContours Métalliques : Les lignes sont tracées avec un placage en nickel noir ou doré, ce qui donne un aspect luxueux et une profondeur saisissante au design.\r\n\r\nDouble Attache Sécurisée : Équipé de deux fermoirs en caoutchouc à l\'arrière pour éviter que le pin\'s ne tourne ou ne tombe lorsqu\'il est fixé sur un sac, une veste ou un panneau de collection.', 5.50, 0, '2026-02-15', 'pin-namsan.jpg', 1, '2026-01-12 17:40:22'),
(40, 8, 'Gochujang', 'Gochujang Traditionnel – Édition \"Saveurs de Corée\"\r\nDécouvrez le cœur de la gastronomie coréenne avec ce pot de Gochujang (pâte de piment fermenté) de qualité premium. Présentée dans un emballage moderne qui respecte les codes traditionnels, cette pâte offre un équilibre parfait entre le piquant, le sucré et l\'umami.\r\n\r\nCaractéristiques du produit :\r\n\r\nFermentation Naturelle : Préparé selon des méthodes ancestrales à partir de poudre de piment rouge séché au soleil, de riz gluant et de soja fermenté, garantissant une profondeur de goût exceptionnelle.\r\n\r\nTexture Riche et Onctueuse : Sa consistance épaisse et veloutée est idéale pour préparer des plats emblématiques comme le Bibimbap, le Tteokbokki ou pour mariner vos viandes pour un barbecue coréen.\r\n\r\nDesign du Packaging : Le pot arbore une étiquette élégante aux couleurs rouge vif et noir, soulignant le caractère épicé du produit. Le couvercle hermétique assure une conservation optimale de la fraîcheur et des arômes après ouverture.\r\n\r\nPolyvalence Culinaire : Bien plus qu\'un simple condiment, c\'est une base culinaire qui apporte une couleur rouge éclatante et une saveur addictive à toutes vos sauces et soupes.', 6.90, 12, NULL, 'gochujang.jpg', 1, '2026-01-12 17:40:22'),
(41, 8, 'Doenjang', 'Doenjang Artisanal – L\'Essence du Terroir Coréen\r\nSouvent comparé au miso mais avec un caractère bien plus affirmé et profond, notre Doenjang est une pâte de soja fermentée riche en saveurs \"umami\". C’est l’ingrédient de base pour réaliser le célèbre Doenjang Jjigae (ragoût de pâte de soja), un plat réconfortant présent sur toutes les tables coréennes.\r\n\r\nDétails du produit :\r\n\r\nGoût Authentique : Contrairement aux produits industriels, ce Doenjang offre une saveur terreuse, salée et complexe, fruit d\'un long processus de fermentation naturelle.\r\n\r\nTexture Granuleuse : La pâte contient de petits morceaux de soja fermenté, preuve d\'une préparation traditionnelle qui préserve les nutriments et apporte de la texture à vos bouillons.\r\n\r\nEmballage Premium : Présenté dans un pot robuste au design épuré, avec une étiquette aux tons naturels (marron et crème) rappelant les jarres en terre cuite traditionnelles (Onggi).\r\n\r\nBienfaits Nutritionnels : Naturellement riche en probiotiques et en protéines végétales, c\'est un allié santé indispensable pour une cuisine équilibrée.\r\n\r\nUtilisation : Ce pot est parfait pour préparer des soupes, des marinades pour le poisson ou la viande, ou pour concocter votre propre Ssamjang (sauce pour barbecue coréen) en le mélangeant avec du Gochujang.', 6.50, 10, NULL, 'doenjang.jpg', 1, '2026-01-12 17:40:22'),
(42, 8, 'Kimchi (bocal)', 'Kimchi de Chou Chinois – Fermentation Traditionnelle\r\nIncontournable de tout repas coréen, ce Kimchi est préparé selon une recette artisanale transmise de génération en génération. Ce bocal renferme tout le piquant, le croquant et la fraîcheur qui font la renommée mondiale de ce super-aliment.\r\n\r\nCaractéristiques et composition :\r\n\r\nIngrédients Frais : Composé de chou chinois (Baechu) rigoureusement sélectionné, de radis blanc, d\'ail, de gingembre et d\'oignons verts.\r\n\r\nLe Piquant Parfait : La robe rouge éclatante provient de l\'utilisation de Gochugaru (poudre de piment coréen) de première qualité, offrant une chaleur équilibrée sans masquer les autres saveurs.\r\n\r\nFermentation Naturelle : Riche en probiotiques naturels, ce Kimchi continue de développer son acidité et sa complexité aromatique au fil du temps.\r\n\r\nPackaging Moderne : Le bocal en verre transparent permet d\'apprécier la texture des légumes. Il est scellé par un couvercle hermétique noir élégant pour préserver tout le croquant et les arômes puissants.\r\n\r\nConseils de dégustation : À déguster frais en accompagnement (Banchan), ou à utiliser bien mûr dans une soupe Kimchi Jjigae ou un riz sauté au kimchi. Son visuel vibrant sur fond noir en fait un produit d\'épicerie fine qui attire l\'œil et met l\'eau à la bouche.', 8.90, 0, '2026-01-28', 'kimchi-jar.jpg', 1, '2026-01-12 17:40:22'),
(43, 8, 'Algues nori', '\r\n\r\nCroustillant & Iodé Connues sous le nom de Gim en Corée, ces feuilles d\'algues séchées et grillées sont un pilier de l\'alimentation coréenne. Appréciées pour leur légèreté et leurs bienfaits nutritionnels, elles apportent une touche marine et une texture unique à tous vos repas.\r\n\r\nCaractéristiques du produit :\r\n\r\nGrillage à la Perfection : Chaque feuille est délicatement grillée pour obtenir un croustillant irrésistible qui fond en bouche.\r\n\r\nAssaisonnement Authentique : Légèrement brossées à l\'huile de sésame grillé et saupoudrées d\'une fine pincée de sel marin pour rehausser leur saveur naturelle.\r\n\r\nSuper-aliment : Naturellement riches en vitamines (A, B12, C), en iode et en minéraux, elles constituent une alternative saine et peu calorique aux chips classiques.\r\n\r\nFormat Polyvalent : Présentées dans un emballage hermétique pour garantir une fraîcheur maximale, elles sont prêtes à être découpées pour accompagner un bol de riz chaud ou pour confectionner vos propres Gimbap maison.\r\n\r\nConseils d\'utilisation : Parfaites pour envelopper une bouchée de riz, pour garnir un ramen ou simplement pour être dégustées telles quelles comme collation saine. Le design élégant du sachet met en avant la qualité premium des algues récoltées dans les eaux pures de Corée.', 4.20, 22, NULL, 'Algues-nori.jpg', 1, '2026-01-12 17:40:22'),
(44, 8, 'Sauce Bulgogi Premium ', '\r\nPlongez dans l\'univers du barbecue coréen avec notre Sauce Bulgogi. Cette préparation \"tout-en-un\" est le secret pour transformer n\'importe quelle viande (bœuf, porc ou même poulet) en un festin tendre, juteux et riche en saveurs sucrées-salées.\r\n\r\nCe qui rend cette sauce unique :\r\n\r\nÉquilibre Parfait : Un mélange savant de sauce soja artisanale, de sucre brun et d\'une touche d\'huile de sésame grillé pour une profondeur de goût inégalée.\r\n\r\nLe Secret du Fruit : Contient de la purée de poire coréenne (baé) et de la pomme, dont les enzymes naturelles agissent pour attendrir la viande tout en apportant une douceur fruitée subtile.\r\n\r\nArômes de Caractère : Infusée avec de l\'ail généreux, du gingembre frais et du poivre noir pour une note finale légèrement relevée.\r\n\r\nPrête à l\'Emploi : Pas besoin de préparation compliquée. Versez, laissez mariner 30 minutes, et saisissez à la poêle ou au grill pour obtenir cette caramélisation parfaite typique de Séoul.\r\n\r\nConseil du Chef : Utilisez-la aussi comme sauce de nappage pour vos légumes sautés ou pour caraméliser du tofu. Son flacon élégant au design moderne trouvera parfaitement sa place dans votre cuisine de passionné.', 5.90, 11, NULL, 'Sauce-Bulgogi.jpg', 1, '2026-01-12 17:40:22'),
(45, 9, 'Bougie “Jeju Citrus”', 'une invitation sensorielle au voyage sur l\'île volcanique de Jeju :\r\n\r\nBougie Parfumée \"Jeju Citrus\" – Éclat de Yuzu & Mandarine\r\nÉvadez-vous vers les vergers ensoleillés de l\'île de Jeju avec cette bougie artisanale. Capturant l\'essence même du Yuzu (Yuza) et du Hallabong (la célèbre mandarine de Jeju), cette bougie diffuse une fragrance pétillante qui purifie l\'atmosphère et éveille les sens.\r\n\r\nPyramide Olfactive :\r\n\r\nNotes de tête : Zeste de Yuzu givré, Citron vert éclatant.\r\n\r\nNotes de cœur : Mandarine Hallabong, Fleur de pamplemousse.\r\n\r\nNotes de fond : Musc blanc, touche de Gingembre frais.\r\n\r\nCaractéristiques du produit :\r\n\r\nCire Naturelle : Fabriquée à partir de cire de soja 100% végétale, garantissant une combustion propre, sans toxines, et une diffusion optimale du parfum.\r\n\r\nMèche en Coton ou Bois : Équipée d\'une mèche de haute qualité pour une flamme stable et une durée de combustion prolongée (environ 45 heures).\r\n\r\nDesign Élégant : Présentée dans un contenant en verre dépoli minimaliste ou un pot ambré, avec une étiquette illustrée rappelant les paysages paisibles de la côte coréenne.\r\n\r\nAmbiance \"Healing\" : Parfaite pour vos moments de détente, de lecture ou de soins personnels (skincare), elle apporte une fraîcheur énergisante et apaisante à votre intérieur.\r\n\r\nL\'expérience Jeju : Allumer cette bougie, c\'est comme ouvrir une fenêtre sur le grand air marin de Corée du Sud, où l\'odeur des agrumes frais se mêle à la douceur de vivre insulaire.', 14.90, 8, NULL, 'Bougie-Jeju.jpg', 1, '2026-01-12 17:40:22'),
(46, 9, 'Mug Hangul', 'Mug en céramique décoré d’inscriptions en Hangul, parfait pour savourer café, thé ou chocolat chaud. Son style minimaliste et coréen apporte une ambiance chaleureuse et originale à ton bureau ou ta cuisine. Un incontournable pour les fans de culture coréenne.', 11.50, 12, NULL, 'Mug-Hangul.jpg', 1, '2026-01-12 17:40:22'),
(47, 9, 'Chaussettes K-style', 'Chaussettes style coréen au design moderne et tendance, parfaites pour compléter un outfit streetwear ou casual. Confortables et respirantes, elles sont idéales pour une utilisation quotidienne, que ce soit en ville, en cours ou à la maison. Leur look K-fashion apporte une touche unique et originale à ton style.', 6.90, 29, NULL, 'Chaussettes-K-style.jpg', 1, '2026-01-12 17:40:22'),
(48, 9, 'Carnet K-Stationery', 'Carnet inspiré de la papeterie coréenne, parfait pour prendre des notes, écrire un journal personnel ou organiser ses journées. Son design doux et esthétique en fait un accessoire idéal pour les étudiants, les créatifs ou les amateurs de K-culture. Léger et pratique, il se glisse facilement dans un sac.', 7.50, 0, '2026-02-08', 'Carnet-K-Stationery.jpg', 1, '2026-01-12 17:40:22');

-- --------------------------------------------------------

--
-- Structure de la table `item_sizes`
--

CREATE TABLE `item_sizes` (
  `item_id` int(11) NOT NULL,
  `size_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `item_sizes`
--

INSERT INTO `item_sizes` (`item_id`, `size_id`) VALUES
(47, 6),
(47, 7),
(47, 8),
(47, 9),
(47, 10),
(47, 11),
(47, 12),
(47, 13),
(47, 14);

-- --------------------------------------------------------

--
-- Structure de la table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `customer_firstname` varchar(100) DEFAULT NULL,
  `customer_lastname` varchar(100) DEFAULT NULL,
  `customer_name` varchar(120) NOT NULL,
  `customer_email` varchar(190) NOT NULL,
  `customer_phone` varchar(30) DEFAULT NULL,
  `customer_address` varchar(255) NOT NULL,
  `customer_postal` varchar(20) DEFAULT NULL,
  `customer_postal_code` varchar(20) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'paid',
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `delivered_at` datetime DEFAULT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `customer_firstname`, `customer_lastname`, `customer_name`, `customer_email`, `customer_phone`, `customer_address`, `customer_postal`, `customer_postal_code`, `status`, `total`, `created_at`, `delivered_at`, `is_archived`) VALUES
(5, 2, NULL, NULL, 'Safaa Zemmar', 'safaazemmar@gmail.com', NULL, '6 rue Albert camus', NULL, NULL, 'paid', 63.80, '2026-01-12 13:34:51', NULL, 0),
(6, 2, NULL, NULL, 'Safaa Zemmar', 'safaazemmar@gmail.com', NULL, '6 rue Albert camus', NULL, NULL, 'shipped', 4.90, '2026-01-12 13:35:08', NULL, 0),
(7, 2, NULL, NULL, 'Safaa Zemmar', 'safaazemmar@gmail.com', NULL, 'tyiikkkj', NULL, NULL, 'paid', 4.90, '2026-01-12 13:47:54', NULL, 1),
(8, 2, NULL, NULL, 'Safaa Zemmar', 'safaazemmar@gmail.com', NULL, '6 rue loo', NULL, NULL, 'cancelled', 4.90, '2026-01-12 13:48:45', NULL, 0),
(9, 2, NULL, NULL, 'Safaa Zemmar', 'safaazemmar@gmail.com', NULL, '6 rue albert camus', NULL, NULL, 'shipped', 132.30, '2026-01-12 13:56:05', NULL, 0),
(10, 2, NULL, NULL, 'Safaa Zemmar', 'safaazemmar@gmail.com', NULL, '10 rue paul valerie', NULL, NULL, 'paid', 4.90, '2026-01-12 14:39:42', NULL, 0),
(11, 2, NULL, NULL, 'Safaa Zemmar', 'safaazemmar@gmail.com', NULL, '6 rue albert camus', NULL, NULL, 'paid', 9.80, '2026-01-12 18:29:25', NULL, 0),
(12, 2, NULL, NULL, 'Safaa Zemmar', 'safaazemmar@gmail.com', NULL, '6 rue albert camus', NULL, NULL, 'paid', 6.90, '2026-01-13 09:27:52', NULL, 0),
(13, NULL, NULL, NULL, 'Safaa Zemmar', 'safouzemmar@gmail.com', NULL, '6 rue albert camus', NULL, NULL, 'paid', 5.90, '2026-01-13 12:17:32', '2026-01-14 12:04:44', 0),
(14, 2, NULL, NULL, 'Safaa Zemmar', 'safaazemmar@gmail.com', NULL, '6 rue albert camus', NULL, NULL, 'delivered', 11.50, '2026-01-14 10:38:33', '2026-01-14 12:02:35', 0),
(15, 5, NULL, NULL, 'Safaa Zemmar', 'sef54094zemmar@gmail.com', NULL, '46 rue dupont', NULL, NULL, 'paid', 11.50, '2026-01-15 09:09:37', NULL, 0),
(16, 5, NULL, NULL, 'Safaa Zemmar', 'sef54094zemmar@gmail.com', NULL, '6 rue rdr', NULL, NULL, 'delivered', 5.90, '2026-01-15 09:11:10', '2026-01-15 09:36:30', 0),
(17, 2, NULL, NULL, 'Safaa Zemmar', 'safaazemmar@gmail.com', NULL, '45  rue de la paix', NULL, NULL, 'paid', 6.90, '2026-01-15 09:38:36', NULL, 0),
(18, 2, 'Safaa', 'Zemmar', '', 'safaazemmar@gmail.com', '0754587377', '6 rue Albert camus', '60100', NULL, 'paid', 5.90, '2026-01-15 09:49:18', NULL, 0),
(19, 2, 'Safaa', 'Zemmar', '', 'safaazemmar@gmail.com', '0754587377', '6 rue Albert camus', '60100', NULL, 'cancelled', 11.50, '2026-01-15 09:49:34', NULL, 0),
(20, 6, 'Safaa', 'Zemmar', '', 'safouzemmar@gmail.com', '0754587377', '6 rue Albert camus', '60100', NULL, 'paid', 6.90, '2026-01-16 20:15:15', NULL, 0),
(21, 2, 'Safaa', 'Zemmar', '', 'safaazemmar@gmail.com', '0754587377', '6 rue Albert camus', '60100', NULL, 'paid', 3.50, '2026-01-16 20:24:56', NULL, 0),
(22, 2, 'Safaa', 'Zemmar', '', 'safaazemmar@gmail.com', '0754587377', '6 rue Albert camus', '60100', NULL, 'delivered', 18.40, '2026-01-20 11:21:55', '2026-01-20 11:22:19', 0),
(23, 2, 'Safaa', 'Zemmar', '', 'safaazemmar@gmail.com', '0754587377', '6 rue Albert camus', '60100', NULL, 'paid', 22.80, '2026-01-23 10:15:38', NULL, 0),
(24, 6, 'Safaa', 'Zemmar', '', 'safouzemmar@gmail.com', '0754587377', '6 rue Albert camus', '60100', NULL, 'delivered', 6.90, '2026-01-28 00:12:23', '2026-02-04 22:37:51', 0),
(25, 6, 'Safaa', 'Zemmar', '', 'safouzemmar@gmail.com', '0754587377', '6 rue Albert camus', '60100', NULL, 'paid', 6.90, '2026-02-04 22:38:49', NULL, 1),
(26, 6, 'Safaa', 'Zemmar', '', 'safouzemmar@gmail.com', '0754587377', '6 rue Albert camus', '60100', NULL, 'paid', 38.00, '2026-02-04 23:05:25', NULL, 0);

-- --------------------------------------------------------

--
-- Structure de la table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `line_total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `item_id`, `quantity`, `unit_price`, `line_total`) VALUES
(1, 5, 5, 1, 2.20, 2.20),
(2, 5, 8, 3, 18.90, 56.70),
(3, 5, 9, 1, 4.90, 4.90),
(4, 6, 9, 1, 4.90, 4.90),
(5, 7, 9, 1, 4.90, 4.90),
(6, 8, 9, 1, 4.90, 4.90),
(7, 9, 8, 7, 18.90, 132.30),
(8, 10, 9, 1, 4.90, 4.90),
(9, 11, 9, 2, 4.90, 9.80),
(10, 12, 47, 1, 6.90, 6.90),
(11, 13, 44, 1, 5.90, 5.90),
(12, 14, 46, 1, 11.50, 11.50),
(13, 15, 46, 1, 11.50, 11.50),
(14, 16, 44, 1, 5.90, 5.90),
(15, 17, 47, 1, 6.90, 6.90),
(16, 18, 44, 1, 5.90, 5.90),
(17, 19, 46, 1, 11.50, 11.50),
(18, 20, 47, 1, 6.90, 6.90),
(19, 21, 22, 1, 3.50, 3.50),
(20, 22, 47, 1, 6.90, 6.90),
(21, 22, 46, 1, 11.50, 11.50),
(22, 23, 26, 1, 7.90, 7.90),
(23, 23, 45, 1, 14.90, 14.90),
(24, 24, 47, 1, 6.90, 6.90),
(25, 25, 47, 1, 6.90, 6.90),
(26, 26, 37, 1, 3.50, 3.50),
(27, 26, 47, 5, 6.90, 34.50);

-- --------------------------------------------------------

--
-- Structure de la table `sizes`
--

CREATE TABLE `sizes` (
  `id` int(11) NOT NULL,
  `code` varchar(10) NOT NULL,
  `label` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sizes`
--

INSERT INTO `sizes` (`id`, `code`, `label`) VALUES
(6, '36', '36'),
(7, '37', '37'),
(8, '38', '38'),
(9, '39', '39'),
(10, '40', '40'),
(11, '41', '41'),
(12, '42', '42'),
(13, '43', '43'),
(14, '44', '44');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(120) NOT NULL,
  `email` varchar(180) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `password_hash`, `role`, `created_at`) VALUES
(1, 'Admin K-Store', 'admin@kstore.local', '$2y$10$abcdefghijklmnopqrstuv', 'admin', '2026-01-12 11:04:32'),
(2, 'Safaa Zemmar', 'safaazemmar@gmail.com', '$2y$10$YPWPqN4rldb64VJ1cFUMLOHlt0U7BLjgiieY..ekU/lNASVnMtCbm', 'admin', '2026-01-12 12:47:38'),
(4, 'saf', 'safsaf@gmail.com', '$2y$10$5B/km.Cg7PiV5pVJGHqwV.GAN3zekrj9S395QG..8CGxvmOHN9g/u', 'admin', '2026-01-14 08:39:59'),
(5, 'Safaa Zemmar', 'sef54094zemmar@gmail.com', '$2y$10$2lr.hl9oks.0bWBgk//7.uRSYbCZQr9RFs5/uH9oSERr5SycnUhBq', 'user', '2026-01-14 08:47:03'),
(6, 'Saf', 'safouzemmar@gmail.com', '$2y$10$RyeWFxhWVpEzAXZ4pjSbauS8Ysb7yexyWTWUrNEJ1XGILuvHBvlyu', 'user', '2026-01-16 19:14:50');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Index pour la table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`),
  ADD KEY `fk_invoices_user` (`user_id`);

--
-- Index pour la table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_items_category` (`category_id`);

--
-- Index pour la table `item_sizes`
--
ALTER TABLE `item_sizes`
  ADD PRIMARY KEY (`item_id`,`size_id`),
  ADD KEY `size_id` (`size_id`);

--
-- Index pour la table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orders_user` (`user_id`);

--
-- Index pour la table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orderitems_order` (`order_id`),
  ADD KEY `fk_orderitems_item` (`item_id`);

--
-- Index pour la table `sizes`
--
ALTER TABLE `sizes`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_users_email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT pour la table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT pour la table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT pour la table `sizes`
--
ALTER TABLE `sizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `fk_invoices_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_invoices_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `fk_items_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `item_sizes`
--
ALTER TABLE `item_sizes`
  ADD CONSTRAINT `item_sizes_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `item_sizes_ibfk_2` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_orderitems_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_orderitems_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

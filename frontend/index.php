<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../backend/admin/config.php';
require_once '../backend/admin/db_connect.php';

// Fetch data from database
try {
  // Fetch about data
  $stmt = $pdo->query("SELECT * FROM about ORDER BY created_at DESC LIMIT 1");
  $aboutData = $stmt->fetch(PDO::FETCH_ASSOC);

  // Fetch education data
  $stmt = $pdo->query("SELECT * FROM education ORDER BY start_date DESC");
  $educationData = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Fetch experience data
  $stmt = $pdo->query("SELECT * FROM experience ORDER BY start_date DESC");
  $experienceData = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  error_log("Error fetching data: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description"
    content="<?php echo htmlspecialchars($aboutData['name'] ?? 'SAS'); ?> - <?php echo htmlspecialchars($aboutData['title'] ?? 'Portfolio'); ?>" />
  <title><?php echo htmlspecialchars($aboutData['name'] ?? 'SAS'); ?> | Resume</title>

  <!-- Preload Critical Assets -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="public/styles.css" />
</head>

<!--<body oncontextmenu="return false">-->

<body>
  <audio id="background-music" loop>
    <source src="public/assets/song.mp3" type="audio/mpeg">
  </audio>

  <div class="audio-control">
    <button id="toggleAudio" class="audio-btn">
      <i class="fas fa-volume-mute"></i>
    </button>
  </div>

  <!-- Particle Background -->
  <div id="particles-js"></div>

  <!-- Header -->
  <header class="site-header">
    <div class="container">
      <div class="nav-links">
    <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']): ?>
            <li><a href="../backend/admin/index.php" class="login-btn">
                <i class="fas fa-tachometer-alt"></i> Dashboard
              </a></li>
          <?php else: ?>
            <li><a href="../backend/admin/login.php" class="login-btn">
                <i class="fas fa-sign-in-alt"></i> Login
              </a></li>
          <?php endif; ?>
          </div>
<!--   <ul class="nav-links right">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            Language <i class="fas fa-chevron-down"></i>
          </a>
          <ul class="dropdown-menu">
            <li><a href="#" id="lang-en">English</a></li>
            <li><a href="#" id="lang-fa">Persian</a></li>
          </ul>
        </li>
      </ul>
-->
      <nav>
        <ul class="nav-links">
          <li><a href="#about">About</a></li>
          <li><a href="#skills">Education</a></li>
          <li><a href="#projects">Experience</a></li>
          <li><a href="#contact">Contact</a></li>
          
        </ul>
      </nav>
    </div>
  </header>

  <!-- Hero Section -->
  <section id="hero" class="hero">
    <div class="container">
      <h1>Hello, I'm <span
          class="animated-name highlight"><?php echo htmlspecialchars($aboutData['name'] ?? ''); ?></span>.</h1>
      <p><?php echo htmlspecialchars($aboutData['title'] ?? ''); ?></p>
    </div>
  </section>

  <!-- About Section -->
  <section id="about" class="about">
    <!-- ... video and overlay remain the same ... -->
    <div class="about-content">
      <div class="container">
        <h2>About Me</h2>
        <div class="profile-image">
          <div class="loading-bar"></div>
          <img src="public/assets/Profile.jpeg" alt="Profile Picture" />
        </div>
        <div class="bio">
          <p><?php echo nl2br(htmlspecialchars($aboutData['bio'] ?? '')); ?></p>
        </div>
      </div>
    </div>
  </section>

  <!-- Skills/Education Section -->
  <section id="skills" class="skills">
    <div class="container">
      <h2>Education</h2>
      <div class="skill-grid">
        <?php if (!empty($educationData)):
          foreach ($educationData as $education): ?>
            <div class="skill-card">
              <h3><?php echo htmlspecialchars($education['institution']); ?></h3>
              <p><?php echo htmlspecialchars($education['degree']); ?></p>
              <p><?php echo htmlspecialchars($education['field']); ?></p>
              <p><?php echo date('Y', strtotime($education['start_date'])); ?> -
                <?php echo $education['end_date'] ? date('Y', strtotime($education['end_date'])) : 'Present'; ?>
              </p>
              <p><?php echo htmlspecialchars($education['description'] ?? ''); ?></p>
            </div>
          <?php endforeach; endif; ?>
      </div>
    </div>
  </section>

  <!-- Experience Section -->
  <section id="projects" class="projects">
    <div class="container">
      <h2>Experience</h2>
      <div class="project-grid">
        <?php if (!empty($experienceData)):
          foreach ($experienceData as $exp): ?>
            <div class="project-card">
              <h3><?php echo htmlspecialchars($exp['position']); ?></h3>
              <h4><?php echo htmlspecialchars($exp['company']); ?></h4>
              <p><?php echo date('M Y', strtotime($exp['start_date'])); ?> -
                <?php echo $exp['end_date'] ? date('M Y', strtotime($exp['end_date'])) : 'Present'; ?>
              </p>
              <p><?php echo nl2br(htmlspecialchars($exp['responsibilities'] ?? '')); ?></p>
              <?php if (!empty($exp['technologies'])): ?>
                <div class="technologies">
                  <?php foreach (explode(',', $exp['technologies']) as $tech): ?>
                    <span class="tech-tag"><?php echo htmlspecialchars(trim($tech)); ?></span>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          <?php endforeach; endif; ?>
      </div>
    </div>
  </section>

  <!-- Contact Section -->
  <section id="contact" class="contact">
    <div class="container">
      <h2 class="section-title">Get in Touch</h2>
      <div class="contact-container">
        <div class="contact-info">
          <div class="contact-header">
            <h3>Contact Information</h3>
            <p>Feel free to reach out through any of these channels</p>
          </div>
          <div class="contact-items">
            <?php
            $stmt = $pdo->query("SELECT * FROM contact_info WHERE is_active = 1 ORDER BY id");
            $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($contacts as $contact):
              if ($contact['type'] !== 'Location'): // Skip location in contact list
                ?>
                <div class="contact-item">
                  <div class="icon-wrapper">
                    <i class="<?php echo htmlspecialchars($contact['icon']); ?>"></i>
                  </div>
                  <div class="contact-details">
                    <h4><?php echo htmlspecialchars($contact['type']); ?></h4>
                    <a href="<?php echo htmlspecialchars($contact['link']); ?>" target="_blank" class="contact-link">
                      <?php echo htmlspecialchars($contact['value']); ?>
                    </a>
                  </div>
                </div>
              <?php
              endif;
            endforeach;
            ?>
          </div>
        </div>
        <div class="map-section">
          <div class="location-header">
            <?php
            $location = array_filter($contacts, function ($contact) {
              return $contact['type'] === 'Location';
            });
            $location = reset($location);
            ?>
            <h3>My Location</h3>
            <p><?php echo htmlspecialchars($location['value'] ?? 'Kabul, Afghanistan'); ?></p>
          </div>
          <div class="map-container">
            <iframe
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3286.9455164176223!2d69.12799147574663!3d34.53399339400089!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x38d16f81545891e3%3A0xf40345309a7a5792!2sKarte%204%2C%20Kabul%2C%20Afghanistan!5e0!3m2!1sen!2s!4v1708439569083!5m2!1sen!2s"
              width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"
              referrerpolicy="no-referrer-when-downgrade">
            </iframe>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <div class="container">
      <p>
        &copy; <span id="current-year"></span>
        <span class="highlight">SAS</span>. All rights reserved.
      </p>
    </div>
  </footer>

  <script>
    document.getElementById("current-year").textContent = new Date().getFullYear();

  </script>
  <!-- Add these before closing </body> tag -->
  
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const loginBtn = document.querySelector('.login-btn');
      if (loginBtn) {
        loginBtn.addEventListener('click', function (e) {
          e.preventDefault();
          window.location.href = this.getAttribute('href');
        });
      }
    });
  </script>

  <!-- Particles.js Library -->
  <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
  <script src="public/script.js"></script>
  <script>
    // Disable right-click
    //document.addEventListener('contextmenu', function(e) {
    //    e.preventDefault();
    // });

    // Disable keyboard shortcuts
    // document.addEventListener('keydown', function(e) {
    //    if (e.ctrlKey || e.keyCode == 123) { // 123 is F12
    //        e.preventDefault();
    //    }
    //});

    // Audio control
    document.addEventListener('DOMContentLoaded', function () {
      const audio = document.getElementById('background-music');
      const toggleBtn = document.getElementById('toggleAudio');
      const icon = toggleBtn.querySelector('i');
      let isPlaying = false;

      // Set initial volume
      audio.volume = 0.3;

      // Function to play audio
      function playAudio() {
        audio.play();
        isPlaying = true;
        icon.className = 'fas fa-volume-up';
      }

      // Function to pause audio
      function pauseAudio() {
        audio.pause();
        isPlaying = false;
        icon.className = 'fas fa-volume-mute';
      }

      // Handle button click
      toggleBtn.addEventListener('click', function () {
        if (isPlaying) {
          pauseAudio();
        } else {
          playAudio();
        }
      });

      // Start playing on first interaction
      document.addEventListener('click', function startPlayback() {
        playAudio();
        document.removeEventListener('click', startPlayback);
      }, { once: true });
    });
  </script>
</body>

</html>
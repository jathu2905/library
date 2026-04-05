<?php include 'includes/header.php'; ?>

    <div class="contactus-container">
        <h2>Contact Us</h2>
        <form action="#" method="POST">
            <input type="text" name="name" placeholder="Your Name" required>
            <input type="email" name="email" placeholder="Your Email" required>
            <textarea name="message" placeholder="Your Message" rows="5" required></textarea>
            <button type="submit" class="btn">Send Message</button>
        </form>

        <!-- Google Maps Embed -->
        <div class="map-container">
            <h3>Our Location</h3>
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3933.3424591242483!2d80.02107167339088!3d9.651751290436556!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3afe56b963ffa9ff%3A0x3cd84b69fd4e0cb0!2z4K6J4K6v4K6w4K-NIOCupOCviuCutOCuv-CuqOCvgeCun-CvjeCuquCuteCuv-Cur-CusuCvjSDgrqjgrr_grrHgr4HgrrXgrqngrq7gr40sIOCur-CuvuCutOCvjeCuquCvjeCuquCuvuCuo-CuruCvjQ!5e0!3m2!1sta!2slk!4v1730210914492!5m2!1sta!2slk"
                width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>

<script>
    // Ensure body class is added for styling
    document.body.classList.add('contactus-page');
</script>

<?php include 'includes/footer.php'; ?>

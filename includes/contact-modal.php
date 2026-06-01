<!-- Contact Modal -->
<div id="contact-modal" class="aura-modal">
    <div class="modal-overlay" onclick="toggleModal('contact-modal')"></div>
    <div class="modal-content glass-morphism">
        <form id="contactForm" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="text" name="name" class="aura-input" placeholder="NAME" required>
            <input type="email" name="email" class="aura-input" placeholder="EMAIL" required>
            <input type="text" name="subject" class="aura-input" placeholder="SUBJECT" required id="modalSubject">
            <textarea name="message" class="aura-input" placeholder="MESSAGE" rows="5" required></textarea>
            <button type="submit" name="send_message" class="cta-shimmer full-width">SEND MESSAGE</button>
        </form>
    </div>
</div>

<?php if (isset($success)): ?>
<div id="success-toast" class="success-toast active">
    Message sent successfully!
</div>
<script>
    setTimeout(() => {
        document.getElementById('success-toast').classList.remove('active');
    }, 3000);
</script>
<?php endif; ?>

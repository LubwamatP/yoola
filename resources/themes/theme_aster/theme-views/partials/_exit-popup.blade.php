{{-- Exit Intent Popup - Captures leaving visitors --}}

<div id="exit-popup" class="exit-popup-overlay" style="display: none;">
  <div class="exit-popup-content">
    <button class="exit-popup-close" onclick="closeExitPopup()">&times;</button>
    <div class="exit-popup-icon">💬</div>
    <h2 class="exit-popup-headline">Wait! Got Questions?</h2>
    <p class="exit-popup-subtext">
      Chat with our team on WhatsApp.<br>
      We'll help you find exactly what you need.
    </p>
    <a href="https://wa.me/256704229768?text=Hi!%20I%20was%20browsing%20yoola.ug%20and%20have%20a%20question" 
       class="exit-popup-cta" onclick="trackExitPopupClick()">
      💬 Chat on WhatsApp
    </a>
    <p class="exit-popup-footer">
      Or call us: <a href="tel:+256704229768">+256 780 221 421</a>
    </p>
    <button class="exit-popup-dismiss" onclick="closeExitPopup()">
      No thanks, I'll keep browsing
    </button>
  </div>
</div>

<style>
.exit-popup-overlay {
  position: fixed; top: 0; left: 0; width: 100%; height: 100%;
  background: rgba(0, 0, 0, 0.7); z-index: 999999;
  display: flex; align-items: center; justify-content: center;
  animation: exitFadeIn 0.3s ease;
}
.exit-popup-content {
  background: white; padding: 40px; border-radius: 16px;
  max-width: 420px; width: 90%; text-align: center;
  position: relative; animation: exitSlideUp 0.3s ease;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}
.exit-popup-close {
  position: absolute; top: 15px; right: 15px;
  background: none; border: none; font-size: 28px;
  cursor: pointer; color: #999; line-height: 1;
}
.exit-popup-close:hover { color: #333; }
.exit-popup-icon { font-size: 60px; margin-bottom: 15px; }
.exit-popup-headline { font-size: 28px; font-weight: 700; color: #1a1a1a; margin: 0 0 10px 0; }
.exit-popup-subtext { font-size: 16px; color: #666; margin: 0 0 25px 0; line-height: 1.5; }
.exit-popup-cta {
  display: inline-block; background: linear-gradient(135deg, #25D366, #128C7E);
  color: white !important; padding: 16px 40px; font-size: 18px; font-weight: 600;
  border-radius: 50px; text-decoration: none;
  transition: transform 0.2s, box-shadow 0.2s;
  box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3);
}
.exit-popup-cta:hover {
  transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37, 211, 102, 0.4); color: white;
}
.exit-popup-footer { font-size: 14px; color: #888; margin: 20px 0 15px 0; }
.exit-popup-footer a { color: #E65100; font-weight: 600; }
.exit-popup-dismiss {
  background: none; border: none; color: #999;
  font-size: 13px; cursor: pointer; text-decoration: underline;
}
.exit-popup-dismiss:hover { color: #666; }
</style>

@verbatim
<style>
@keyframes exitFadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes exitSlideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
@media (max-width: 480px) {
  .exit-popup-content { padding: 30px 20px; }
  .exit-popup-headline { font-size: 24px; }
  .exit-popup-cta { padding: 14px 30px; font-size: 16px; }
}
</style>

<script>
let exitPopupShown = false;
let exitPopupCooldown = 86400000; // 24 hours

function shouldShowExitPopup() {
  const lastShown = localStorage.getItem('exitPopupLastShown');
  if (lastShown) {
    const timeSince = Date.now() - parseInt(lastShown);
    if (timeSince < exitPopupCooldown) return false;
  }
  return true;
}

function showExitPopup() {
  if (exitPopupShown || !shouldShowExitPopup()) return;
  exitPopupShown = true;
  document.getElementById('exit-popup').style.display = 'flex';
  localStorage.setItem('exitPopupLastShown', Date.now().toString());
  if (typeof gtag === 'function') {
    gtag('event', 'exit_popup_shown', { 'event_category': 'engagement' });
  }
}

function closeExitPopup() {
  document.getElementById('exit-popup').style.display = 'none';
}

function trackExitPopupClick() {
  if (typeof gtag === 'function') {
    gtag('event', 'exit_popup_cta_click', { 'event_category': 'conversion' });
  }
}

// Exit intent detection
document.addEventListener('mouseout', function(e) {
  if (e.clientY < 10 && e.relatedTarget === null) {
    showExitPopup();
  }
});

// Close on overlay click
document.getElementById('exit-popup').addEventListener('click', function(e) {
  if (e.target === this) closeExitPopup();
});

// Close on Escape key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeExitPopup();
});
</script>
@endverbatim

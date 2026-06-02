
<footer>
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-4">
        <div class="brand mb-3">MealConnect</div>
        <p style="font-size:0.88rem;line-height:1.7;max-width:280px;">Premium food ordering experience. Fresh ingredients, bold flavors, delivered fast to your door.</p>
        <div class="d-flex gap-3 mt-3">
          <a href="#" style="color:#C0392B;font-size:1.1rem;"><i class="fab fa-facebook-f"></i></a>
          <a href="#" style="color:#C0392B;font-size:1.1rem;"><i class="fab fa-instagram"></i></a>
          <a href="#" style="color:#C0392B;font-size:1.1rem;"><i class="fab fa-twitter"></i></a>
        </div>
      </div>
      <div class="col-lg-2 col-6">
        <h6>Quick Links</h6>
        <a href="index.php">Home</a>
        <a href="menu.php">Menu</a>
        <a href="contact.php">Contact</a>
        <a href="login.php">Sign In</a>
      </div>
      <div class="col-lg-2 col-6">
        <h6>Categories</h6>
        <a href="menu.php?cat=1">Burgers</a>
        <a href="menu.php?cat=2">Pizza</a>
        <a href="menu.php?cat=3">Fried Chicken</a>
        <a href="menu.php?cat=4">Drinks</a>
      </div>
      <div class="col-lg-4">
        <h6>Contact Us</h6>
        <p style="font-size:0.88rem;"><i class="fas fa-map-marker-alt me-2" style="color:#C0392B;"></i>University Road, Bahawalpur</p>
        <p style="font-size:0.88rem;margin-top:0.5rem;"><i class="fas fa-phone me-2" style="color:#C0392B;"></i>+92 300 4232350</p>
        <p style="font-size:0.88rem;margin-top:0.5rem;"><i class="fas fa-envelope me-2" style="color:#C0392B;"></i>hello@mealconnect.com</p>
        <p style="font-size:0.88rem;margin-top:0.5rem;"><i class="fas fa-clock me-2" style="color:#C0392B;"></i>Daily: 10:00 AM – 11:00 PM</p>
      </div>
    </div>
    <div class="footer-bottom">
      <p>&copy; <?= date('Y') ?> MealConnect. All rights reserved. Crafted with passion.</p>
    </div>
  </div>
</footer>

<!-- AI Chatbot -->
<button id="chatbot-btn" title="Ask our AI Assistant"><i class="fas fa-robot"></i></button>
<div id="chatbot-window">
  <div id="chatbot-header">
    <h6><i class="fas fa-robot me-2"></i>MealConnect AI</h6>
    <button onclick="document.getElementById('chatbot-window').style.display='none'" style="background:none;border:none;color:#fff;font-size:1rem;cursor:pointer;"><i class="fas fa-times"></i></button>
  </div>
  <div id="chatbot-messages">
    <div class="chat-msg bot">Hi! I'm your MealConnect AI assistant. Ask me about our menu, recommendations, or anything food-related!</div>
  </div>
  <div id="chatbot-input-area">
    <input type="text" id="chatbot-input" placeholder="Ask me anything..." onkeypress="if(event.key==='Enter')sendChat()">
    <button id="chatbot-send" onclick="sendChat()"><i class="fas fa-paper-plane"></i></button>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('chatbot-btn').addEventListener('click', () => {
  const w = document.getElementById('chatbot-window');
  w.style.display = w.style.display === 'flex' ? 'none' : 'flex';
  w.style.flexDirection = 'column';
});
async function sendChat() {
  const inp = document.getElementById('chatbot-input');
  const msg = inp.value.trim();
  if (!msg) return;
  const msgs = document.getElementById('chatbot-messages');
  msgs.innerHTML += `<div class="chat-msg user">${msg}</div>`;
  inp.value = '';
  msgs.scrollTop = msgs.scrollHeight;
  try {
    const res = await fetch('chatbot_api.php', {method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({message:msg})});
    const data = await res.json();
    msgs.innerHTML += `<div class="chat-msg bot">${data.reply || 'Sorry, I could not process that.'}</div>`;
  } catch(e) {
    msgs.innerHTML += `<div class="chat-msg bot">Connection error. Please try again.</div>`;
  }
  msgs.scrollTop = msgs.scrollHeight;
}
</script>
</body>
</html>

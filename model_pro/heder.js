function loadHeader() {
  document.getElementById("header").innerHTML = `
    <header style="background:black;color:red;padding:15px;">
      <h1>Clothing Store</h1>
      <nav>
        <a href="index.html" style="color:white;margin:10px;">Home</a>
        <a href="products.html" style="color:white;margin:10px;">Products</a>
        <a href="cart.html" style="color:white;margin:10px;">Cart</a>
      </nav>
    </header>
  `;
}

loadHeader();
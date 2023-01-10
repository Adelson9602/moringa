let Products_market = localStorage.getItem("Products_market")
  ? JSON.parse(localStorage.getItem("Products_market"))
  : [];
//const URL_IMAGE = "http://192.168.0.210/Developed_Programming/97-Moringa/dev/gesadmin/resources/assets/Items/";
const URL_IMAGE = "https://gesadmin.com.co/ges/moringa/resources/assets/Items/";



const MoneyFormart = (num) => {
  if (!num || num == "NaN") return 0;
  if (num == "Infinity") return "&#x221e;";
  num = num.toString().replace(/\$|\,/g, "");
  if (isNaN(num)) num = "0";
  sign = num == (num = Math.abs(num));
  num = Math.floor(num * 100 + 0.50000000001);
  cents = num % 100;
  num = Math.floor(num / 100).toString();
  if (cents < 10) cents = "0" + cents;
  for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++)
    num =
      num.substring(0, num.length - (4 * i + 3)) +
      "." +
      num.substring(num.length - (4 * i + 3));
  return (sign ? "" : "-") + num;
};

const modUnitsProduct = (type) => {
  let valueInputCant = $("#cant").val();

  if (type == "add") {
    $("#cant").val(++valueInputCant);
  } else {
    $("#cant").val(valueInputCant <= 1 ? 1 : --valueInputCant);
  }
};

const consultAllProducts = async () => {
  let products = await consultProducts();
  localStorage.setItem("dataProducts", JSON.stringify(products));
};

const showAllGroups = async () => {
  let groups = await consultAllGroups()

  let groupsHtml = ``

  // <img src="https://gesadmin.com.co/ges/moringa/resources/assets/Groups/${group.Image}" alt="">
  groupsHtml = groups.map( group => {
      return `
      <a class="categoriesProducts-item carousel-cell" href="products.html?id_group=${group.Code}">
        <div class="diammond-image">
          <img src="https://gesadmin.com.co/ges/moringa/resources/assets/Groups/${group.Image}" alt="">
        </div>
        <h3>${group.Name}</h3>
      </a>`
  })
  
  $('#categoriesProducts').html(groupsHtml)
  
  $('.main-carousel').flickity({
      cellAlign: 'left',
      contain: true,
      pageDots: false,
      freeScroll: true,
      prevNextButtons: false,

  });

  var $carousel = $('.main-carousel').flickity()

  $('.arrowCarousel-left').on( 'click', function() {
    $carousel.flickity('previous');
  });

  $('.arrowCarousel-right').on( 'click', function() {
    $carousel.flickity('next');
  });

}

const showGroupsForFilters = async () => {
  let filters = await consultAllGroups();
  let filtersHtml = ``;

  filtersHtml = filters.map((filter, index) => {
    return `
    <li class="dropdown-item">
        <label for="fliterCategory${++index}">
            <input type="checkbox" class="fliterCategory" name="fliterCategory${index}" id="fliterCategory${index}" value="${
      filter.Code
    }">
            ${filter.Name}
        </label> 
    </li>`;
  });

  $("#shopCategories").html(filtersHtml);
};

const applyFilters = async () => {
  let filters = [];

  $(".fliterCategory:checked").each(function (i, e) {
    filters.push($(e).val());
  });

  showProducts(0, filters);
};

const showProducts = async (limit = 0, groups = [], pageSize = 12) => {
  let products = await consultProducts(groups, limit);
  let productsHtml = ``;

  $("#pagination").pagination({
    dataSource: products,
    pageSize: pageSize,
    className: "paginationjs-big",
    callback: function (data, pagination) {
      productsHtml = data.map((product) => {
        let price =
          product.Price_Distributor > 0
            ? product.Price_Distributor
            : product.Price;
        return `
        <div class="col-12 col-sm-4 col-md-4 col-lg-2 col-xxl-2">
          <div class="card card-product border-radius-20 border-0">
            <div id="imagen" style="background-image: url('${URL_IMAGE+''+product.Image}')">
                <div id="info">
                    <div id="descripcion">${product.Product}</div>
                </div>
            </div>
            <div class="card-body">
                <h5 class="card-title text-center title mb-0">$ ${MoneyFormart(price)}</h5>
                <hr class="divider bg-c-primary mt-2 mb-2">
                <div class="d-flex justify-content-around">
                    <a href="#" class="btn btn-light bg-white btn-car ${Number(product.cantidad) == 0 ? 'disabled-link' : '' }"  onclick="addProductMarket(${product.Id})"><i class="fa-solid fa-cart-shopping"></i></a>
                    <a href="product.html?id_product=${product.Id}" class="btn btn-light bg-white btn-car"><i class="fa-solid fa-eye"></i></a>
                </div>
            </div>
          </div>
        </div>`;
      });

      $("#listProducts").html(productsHtml);
    },
  });
};

const consultInfoProduct = (id_product) => {
  let Products = JSON.parse(localStorage.getItem("dataProducts"));
  let product = Products.find((Producto) => Producto.Id == id_product);
  console.log(product.cantidad);

  let price =
    product.Price_Distributor > 0 ? product.Price_Distributor : product.Price;

  $("#info_product").html(`
    <div class="product">
        <div class="product-image">
            <img src="${URL_IMAGE + "" + product.Image }" alt="imagen-producto">
        </div>
        <div class="product-info">
            <h1>${product.Product}</h1> 
            <p>$ ${MoneyFormart(price)} COP</p>
            <div class="productInfo-cant">
                <button type="button" class="button-cant" id="buttoCant-minus" onclick="modUnitsProduct('rem')"> - </button>
                <input type="number" name="cant" id="cant" value="1" min="1"/>
                <button type="button" class="button-cant" id="buttoCant-plus" onclick="modUnitsProduct('add')"> +</button>
            </div>
            <div class="productInfo-button">
                <button type="button" ${Number(product.cantidad) == 0 ? 'disabled' : '' } class="button-cantAdd ${Number(product.cantidad) == 0 ? 'disabled-link' : '' }" onclick="addProductMarket(${
                  product.Id
                }, $('#cant').val())"> 
                    <i class="fa-solid fa-cart-plus"></i> Añadir al carrito
                </button>
            </div>
        </div>
    </div>
  `);
};

const loadProductsMarket = (show) => {
  if (!Products_market) {
    return false;
  }

  $("#list_products_cart").html("");
  if (show) {
    $(".cart").css('right', '0');
    $(".cart").css('display', 'block');
    $("#icon_cart_click").attr("onclick", "loadProductsMarket(false)");
  } else {
    $(".cart").css('right', '-500px');
    $("#icon_cart_click").attr("onclick", "loadProductsMarket(true)");
  }

  if (Products_market.length > 0) {

    let total = 0;
    let subtotal = 0;
    let totalCant = 0;
    Products_market.forEach((product, i) => {
      let price =
        product.Price_Distributor > 0
          ? product.Price_Distributor
          : product.Price;

      subtotal = Number(price * product.Cant);
      total += Number(subtotal);

      totalCant += parseInt(product.Cant);

      $("#list_products_cart").append(`
        <div class="product_cart">
            <div class="delete_product__cart" onclick="removeProductMarket(${i})">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="product_cart__image">
              <img src="${URL_IMAGE+''+product.Image}" alt="">
            </div>
            <div class="product_cart__info">
                <p>${product.Product}</p>
                <p class="price"><strong>Precio: </strong>$ ${MoneyFormart(price)}</p>
                <p class="cant"><strong>Cant: </strong>${product.Cant}</p>
            </div>
        </div>
      `);
    });

    $(".number_products").html(`<p>${totalCant}</p>`);

    $("#total_cart").html(`
      <p class="total_cart__subtotal">Subtotal</p>
        <p class="total_cart__price">$ ${MoneyFormart(
          total
        )} COP
      </p>
    `);
    $("#btn_continue_market").show();
  } else {
    $(".number_products").html(`<p> 0 </p>`);
    $("#list_products_cart").html(`
      <div class="no_products__cart">
          <h4>Sin productos</h4>
      </div>
    `);

    $("#total_cart").html(`
      <p class="total_cart__subtotal">Subtotal</p>
      <p class="total_cart__price">$ 0 COP</p>
    `);
    $("#btn_continue_market").hide();
    $(".cart").hide();
  }
};

const addProductMarket = (id_prod, cant = 1) => {
  let Products = JSON.parse(localStorage.getItem("dataProducts"));
  let product = Products.find((Producto) => Producto.Id == id_prod);

  let find = Products_market.find((e) => e.Id == product.Id);

  if (find) {
    let CantFind = parseInt(find.Cant);
    find.Cant = parseInt(cant) + CantFind;
  } else {
    if (cant > 1) product.Cant = cant;
    product.Image = encodeURIComponent(product.Image);
    Products_market.push(product);
  }

  localStorage.setItem("Products_market", JSON.stringify(Products_market));
  loadProductsMarket();

  const Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.addEventListener("mouseenter", Swal.stopTimer);
      toast.addEventListener("mouseleave", Swal.resumeTimer);
    },
  });

  Toast.fire({
    icon: "success",
    title: "producto añadido correctamente",
  });
};

const removeProductMarket = (index, functionReload = false) => {
  Products_market.splice(index, 1);
  localStorage.setItem("Products_market", JSON.stringify(Products_market));

  if (functionReload === true) loadProductsMarketCart();

  loadProductsMarket(false);
};

const updateProductMarketCant = (id_product, value) => {
  Products_market = Products_market.map((product) => {
    if (product.Id == id_product) {
      return { ...product, Cant: value };
    }

    return product;
  });

  localStorage.setItem("Products_market", JSON.stringify(Products_market));
  loadProductsMarketCart();
};

const loadProductsMarketCart = () => {
  $("#list_products_cart_total").html("");
  if (Products_market.length > 0) {
    let total = 0;
    let subtotal = 0;
    let totalCant = 0;

    Products_market.forEach((product, i) => {
      let price = product.Price_Distributor > 0 ? product.Price_Distributor : product.Price;
      subtotal = Number(price * product.Cant);
      total += Number(subtotal);
      totalCant += parseInt(product.Cant);

      $("#list_products_cart_total").append(`
        <div class="resumCart-item">
          <div class="itemCart-imgae"><img src="${URL_IMAGE + "" + product.Image}" alt=""></div>
          <div class="itemCart-info">
            <h3> ${product.Name_Group}</h3>
            <p class="nameProduct"> ${product.Product}</p>
            <p class="priceProduct"> <strong>Precio:</strong> $ ${MoneyFormart(price)}</p>
            <p class="cantProduct"> 
              <label for="cant"><strong>Cant:</strong></label> 
              <input type="number" name="cant${product.Id}" id="cant${product.Id}" value="${product.Cant}" min="1" onchange="updateProductMarketCant(${product.Id}, this.value)"/>
            </p>
            <p><strong>Total:</strong> $ ${MoneyFormart(subtotal)}</p>
          </div>

          <div class="itemCart-delete" onclick="removeProductMarket(${i}, true)">
            <i class="fas fa-times-circle"></i>
          </div>
        </div>
      `);
    });

    $("#subtotal_info").html(`
      <div class="infoSubtotal-text">
          <p><strong>Total Productos</strong></p>
          <p>${totalCant}</p>
      </div>

      <div class="infoSubtotal-text">
          <p><strong>Subtotal</strong></p>
          <p>$ ${MoneyFormart(total)} COP</p>
      </div>
    `);

    $("#total_info").html(`
      <p>TOTAL</p>
      <p>$ ${MoneyFormart(total)} COP</p>
    `);

    $("#btn_continue_market").show();
  } else {
    $(".number_products").html(`<p> 0 </p>`);
    $("#list_products_cart").html(`
      <div class="no_products__cart">
          <h4>Sin productos</h4>
      </div>
    `);

    $("#total_cart").html(`
      <p class="total_cart__subtotal">Subtotal</p>
      <p class="total_cart__price">$ 0 COP</p>
    `);

    $("#btn_continue_market").hide();
    $(".cart").hide();
  }
};

const sendSearchProduct = (keyword) =>{
  window.open(`./search.html?keyword=${keyword}`, 'blank');
}

const searchProduct = async () => {
  const url = new URL(window.location);
  const keyword = url.searchParams.get("keyword");
  
  const pageSize = 12
  const products = await consultSearchProduct(keyword);
  let productsHtml = ``;

  $("#pagination").pagination({
    dataSource: products,
    pageSize: pageSize,
    className: "paginationjs-big",
    callback: function (data, pagination) {
      productsHtml = data.map((product) => {
        let price =
          product.Price_Distributor > 0
            ? product.Price_Distributor
            : product.Price;
        return `
        <div class="col-12 col-sm-4 col-md-4 col-lg-2 col-xxl-2">
          <div class="card card-product border-radius-20 border-0">
            <div id="imagen" style="background-image: url('${URL_IMAGE+''+product.Image}')">
                <div id="info">
                    <div id="descripcion">${product.Product}</div>
                </div>
            </div>
            <div class="card-body">
                <h5 class="card-title text-center title mb-0">$ ${MoneyFormart(price)}</h5>
                <hr class="divider bg-c-primary mt-2 mb-2">
                <div class="d-flex justify-content-around">
                    <a href="#" class="btn btn-light bg-white btn-car" onclick="addProductMarket(${product.Id})"><i class="fa-solid fa-cart-shopping"></i></a>
                    <a href="product.html?id_product=${product.Id}" class="btn btn-light bg-white btn-car"><i class="fa-solid fa-eye"></i></a>
                </div>
            </div>
          </div>
        </div>`;
      });

      $("#listProducts").html(productsHtml);
    },
  });}

$(document).ready(function () {
  loadProductsMarket(false);
  consultAllProducts();

  showAllGroups();

  $('#search-form').on('submit', (e) => { 
    e.preventDefault();
    const keyword = $('#search-product-input').val();

    sendSearchProduct(keyword)
  })
});

$(window).scroll(() => {
  if ($(".custom-navbar").offset().top > 700) {
    $(".custom-navbar").addClass("no-opacity");
  } else {
    $(".custom-navbar").removeClass("no-opacity");
  }
});

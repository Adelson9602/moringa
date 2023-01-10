const URL_IMAGE_GESCON= "https://tiendamoringa.com/administracion/resources/assets"
//const URL_IMAGE_GESCON= "http://192.168.0.210/Developed_Programming/97-Moringa/dev/administracion/resources/assets"

const showDataAbout = async () => {
  let data = await consultInfoAbout();

  $("#about").html(data[0].tg_2);
  $("#mision").html(data[0].tg_3);
  $("#vision").html(data[0].tg_4);
  $("#history1").html(data[0].tg_5);
  $("#history2").html(data[0].tg_6);
};

const showDataContact = async () => {
  let data = await consultInfoContact();

  $("#whatsapp_footer").html(data[0].tt_1);
  $("#whatsapp_float").attr("href", `https://wa.me/${data[0].tt_1}`);
  $("#address_footer").html(data[0].tt_3);
  $("#email_footer").html(data[0].tt_4);
  $("#instagram_footer").attr("href", data[0].tt_6);
  $("#facebook_footer").attr("href", data[0].tt_5);
  
};

const showAllPost = async () => {
  let data = await consultAllPost();
  let htmlResponse = ``;

  $("#pagination").pagination({
    dataSource: data,
    pageSize: 6,
    className: "paginationjs-big",
    callback: function (data, pagination) {
        htmlResponse = data.map((post) => {
            return `
                    <div class="container-pre-post__blog">
                        <div class="pre-icon-post__blog">
                            <img src="${URL_IMAGE_GESCON}/Method_blog/img/${post.img_1}" alt="">
                        </div>
                        <div class="title-post__blog">
                            <h2>${post.tt_2}</h2>
                        </div>
                        <div class="pre-info-post__blog">
                            <p>${post.tg_3}</p>
                        </div>
                        <div class="btn-ver-post__blog">
                            <a class="btn-ver-post__blog-oficial" href="post.html?id_post=${post.Code}" target="_blank">
                                Ver el post completo
                            </a>
                        </div>
                    </div>
                `;
            });
            
            $("#listPost").html(htmlResponse);
        },
    });
};

const showPost = async (id_post) => {
  let data = await consultPost(id_post);

  let htmlResponse = `
    <div class="title-blog">
        <img src="img/logo_moringa_dark.png" alt="">
    </div>

    <div class="container-title-full-size-post">
        <div class="title-full-size-post">
            <h1>${data[0].tt_2}</h1>
            <h4>${data[0].tg_3}</h4>

            <img src="${URL_IMAGE_GESCON}/Method_blog/img/${data[0].img_1}" alt="">
        </div>
    </div>

    <div class="container-text-full-size-post">
        <div class="text-full-size-post">
            <p>${data[0].tg_4}</p>
        </div>
    </div>

    <div class="container-video-full-size-post">
        <div class="video-full-post" autoplay controls
        style="
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;">
        <iframe
            src="${data[0].url_5}"
            title="YouTube video player" frameborder="0"
            allow="accelerometer; autoplay; clipboard-write;
            encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen="" style="
            width: 100%;
            height: 600px;
            margin: 0 auto;
            ">
        </iframe>
        </div>
    </div>`;

  $("#infoPost").html(htmlResponse);

};

const showNewBanner = async () => {
  
    let newBanner = await consultNewBanner();
    let newBannerHtml = `
      <img id="main-banner__moringa" src="${URL_IMAGE_GESCON}/Method_cambiar_banner/img/${newBanner[0].img_1}" alt="banner principal">
    `;
  
    $("#banner-main__index").html(newBannerHtml);
}
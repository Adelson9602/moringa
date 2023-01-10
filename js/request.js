// consultas "API" Tienda gesadmin
const URL_API = 'https://gesadmin.com.co/ges/moringa/config/tienda';
//const URL_API = 'http://192.168.0.210/Developed_Programming/97-Moringa/dev/gesadmin/config/tienda';
const URL_API_GESCON= 'https://tiendamoringa.com/administracion/config/page';

const consultAllGroups = async () => {
  let headers = new Headers();

  headers.append('Content-Type', 'application/json');
  headers.append('Accept', 'application/json');
  headers.append('Origin','http://localhost:8080');

    try {
      let response = await fetch( URL_API + '/groups.php',{
        mode:'cors',
        headers : headers
      });
        
      let json = await response.json();
      return json
    } catch (error) {
      console.error(error);
    }
}

// Filtro por grupo y número de productos listados
const consultProducts = async (groups = [], numItems = 0)  => {

  let formData = new FormData();

  formData.append('Groups', JSON.stringify(groups));
  formData.append('NumItems', numItems);

  try {
    let response = await fetch( URL_API + '/products.php', {
      mode:'cors',
      method: 'POST',
      body: formData
    });
      
    let json = await response.json();
    return json
  } catch (error) {
    console.error(error);
  }
}

const consultSearchProduct= async (Keyword, numItems = 0)  => {

  let formData = new FormData();

  formData.append('NumItems', numItems);
  formData.append('Keyword', Keyword);

  try {
    let response = await fetch( URL_API + '/product_search.php', {
      mode:'cors',
      method: 'POST',
      body: formData
    });
      
    let json = await response.json();
    return json
  } catch (error) {
    console.error(error);
  }
}

const consultInfoAbout = async ()  => {

  try {
    let response = await fetch( URL_API_GESCON + '/about.php', {
      mode:'cors',
      method: 'POST'
    });
      
    let json = await response.json();
    return json
  } catch (error) {
    console.error(error);
  }
}

const consultInfoContact = async ()  => {

  try {
    let response = await fetch( URL_API_GESCON + '/contact.php', {
      mode:'cors',
      method: 'POST'
    });
      
    let json = await response.json();
    return json
  } catch (error) {
    console.error(error);
  }
}

const consultNewBanner = async () => {

  try {
    let response = await fetch( URL_API_GESCON + '/newBanner.php', {
      mode:'cors',
      method: 'POST'
    });

    let json = await response.json();
    return json
  } catch (error) {
    console.error(error);
  }

}

const consultAllPost = async () => {
  try{
    let response = await fetch ( URL_API_GESCON + '/blog.php', {
      mode:'cors',
      method: 'POST'
    })

    let json = await response.json();
    return json
  }catch (error) {
    console.error(error);
  }
}

const consultPost = async (id_post) => {

  let formData = new FormData();

  formData.append('id_post', id_post);

  try{
    let response = await fetch ( URL_API_GESCON + '/blog.php', {
      mode:'cors',
      method: 'POST',
      body: formData
    })

    let json = await response.json();
    return json
  }catch (error) {
    console.error(error);
  }
}






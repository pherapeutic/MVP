import axios from 'axios';

//const apiStaticPath = 'https://pherapeutic.itechnolabs.tech/api/v1/';
const apiStaticPath = 'https://admin.pherapeutic.com/api/v1/';

const APICaller = (endpoint, method, body, headers) =>
  axios({
    url: `${apiStaticPath}${endpoint}`,
    method: method || 'GET',
    data: body,
    headers: headers || {
      'Content-Type': 'application/json',
      Accept: 'application/json',
    },
    responseType: 'json',
  })
    .then((response) => {
      console.log('response from ', endpoint, ' => ', response);
      return response;
    })
    .catch((error) => {
      console.log('error from ', endpoint, ' => ', error);
      throw error.response;
    });

export default APICaller;

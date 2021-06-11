import React, {useState, useEffect} from 'react';
import {
  View,
  TouchableOpacity,
  Text,
  Image,
  Dimensions,
  TextInput,
} from 'react-native';
import {connect} from 'react-redux';
import MapView, {
  AnimatedRegion,
  Marker,
  PROVIDER_GOOGLE,
} from 'react-native-maps';
import styles from './styles';
import constants from '../../utils/constants';
import APICaller from '../../utils/APICaller';
import {saveUserProfile} from '../../redux/actions/user';
import AwesomeAlert from 'react-native-awesome-alerts';
import LinearGradient from 'react-native-linear-gradient';
import Header from '../../components/Header';
import Modal from 'react-native-modal';
const {height, width} = Dimensions.get('window');
export const mapStyle = [
  {
    elementType: 'geometry',
    stylers: [
      {
        color: '#f5f5f5',
      },
    ],
  },
  {
    elementType: 'labels.icon',
    stylers: [
      {
        visibility: 'off',
      },
    ],
  },
  {
    elementType: 'labels.text.fill',
    stylers: [
      {
        color: '#616161',
      },
    ],
  },
  {
    elementType: 'labels.text.stroke',
    stylers: [
      {
        color: '#f5f5f5',
      },
    ],
  },
  {
    featureType: 'administrative.land_parcel',
    elementType: 'labels.text.fill',
    stylers: [
      {
        color: '#bdbdbd',
      },
    ],
  },
  {
    featureType: 'poi',
    elementType: 'geometry',
    stylers: [
      {
        color: '#eeeeee',
      },
    ],
  },
  {
    featureType: 'poi',
    elementType: 'labels.text.fill',
    stylers: [
      {
        color: '#757575',
      },
    ],
  },
  {
    featureType: 'poi.park',
    elementType: 'geometry',
    stylers: [
      {
        color: '#e5e5e5',
      },
    ],
  },
  {
    featureType: 'poi.park',
    elementType: 'labels.text.fill',
    stylers: [
      {
        color: '#9e9e9e',
      },
    ],
  },
  {
    featureType: 'road',
    elementType: 'geometry',
    stylers: [
      {
        color: '#ffffff',
      },
    ],
  },
  {
    featureType: 'road.arterial',
    elementType: 'labels.text.fill',
    stylers: [
      {
        color: '#757575',
      },
    ],
  },
  {
    featureType: 'road.highway',
    elementType: 'geometry',
    stylers: [
      {
        color: '#dadada',
      },
    ],
  },
  {
    featureType: 'road.highway',
    elementType: 'labels.text.fill',
    stylers: [
      {
        color: '#616161',
      },
    ],
  },
  {
    featureType: 'road.local',
    elementType: 'labels.text.fill',
    stylers: [
      {
        color: '#9e9e9e',
      },
    ],
  },
  {
    featureType: 'transit.line',
    elementType: 'geometry',
    stylers: [
      {
        color: '#e5e5e5',
      },
    ],
  },
  {
    featureType: 'transit.station',
    elementType: 'geometry',
    stylers: [
      {
        color: '#eeeeee',
      },
    ],
  },
  {
    featureType: 'water',
    elementType: 'geometry',
    stylers: [
      {
        color: '#c9c9c9',
      },
    ],
  },
  {
    featureType: 'water',
    elementType: 'labels.text.fill',
    stylers: [
      {
        color: '#9e9e9e',
      },
    ],
  },
];

const TherapistStatus = (props) => {
  const [showAlert, setShowAlert] = useState(false);
  const [showAlertPayment, setshowAlertPayment] = useState(false);
  const [showAlertMessage,setshowAlertmessage] = useState(false);
  
  const [showmessage, setshowmessage] = useState('');
  
  const [location, setLocation] = useState('');
  const [markers, setmarkers] = useState();
  // [{
  //   title: 'hello',
  //   coordinates: {
  //     latitude: 23.54,
  //     longitude: 87.70
  //   },
  // },
  // {
  //   title: 'hello',
  //   coordinates: {
  //     latitude: 23.60,
  //     longitude: 87.24

  //   },  

  // }]
  const {userToken, userData, navigation, dispatch} = props;
  const {online_status} = userData;
  const [client_id, setclient_id] = useState('');
  const [stripe_connect_id, setstripe_connect_id] = useState('');
  const [stripe_connect_url, setStripe_connect_url] = useState('');
  const [isModalVisible, setModalVisible] = useState(false);
  const [lastModalVisible, setlastModalVisible] = useState(0);
  useEffect(() => {
    console.log('online userData => ', userData);
    console.log('online status => ', online_status);
    getaddress()
    clientList()
    getstripeData()
    getUserProfile()
  }, [online_status]);

  const StatusChanged = () => {
    if(stripe_connect_id==null)
    {
      if(lastModalVisible==0 && online_status==0) 
      {
      setModalVisible(true);
      }
      else{
      changeOnlineStatus()
      }
     // setshowAlertPayment(true)
    }
    else{
      changeOnlineStatus()
    }
  };


  const changeOnlineStatus = () => {
   
    const endpoint = 'user/changeOnlineStatus';
    const method = 'GET';
    const headers = {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${userToken}`,
      Accept: 'application/json',
    };
    APICaller(endpoint, method, null, headers)
      .then((response) => {
        console.log('=====> ', response['data']['data']['online_status']);
        const {status, statusCode, message, data} = response['data'];
        if (status === 'success') {
         
          dispatch(saveUserProfile(data));
          // setAlert('Profile Update Successfully')
          setShowAlert(true);
          setlastModalVisible(0)
        }
      })
      .catch((error) => {
        const {data} = error;
        if (data.statusCode === 401) {
          // if(data.message=="Unable to connect stripe")
          // {
          //   setshowAlertPayment(true)
           
          // }
          // else 
          // //if(data.message=="Your stripe account not verified.")
          // {
           //  setshowAlertmessage(true)
            // setshowmessage(data.message)
         // }

        }
        console.log('error changing online status => ', error['data']);
      });
  };
  const getstripeData = () => {
    const endpoint = 'stripeData';
    const method = 'GET';
    const headers = {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${userToken}`,
      Accept: 'application/json',
    };
    //consoler.log(headers)
    APICaller(endpoint, method, null, headers)
      .then((response) => {
        //  console.log('response getting user profile => ', response['data']);
        const {status, statusCode, message, data} = response['data'];
        if (status === 'success') {
          // console.log(data.client_id)
          //  this.setState('client_id',data.client_id)
          setclient_id(data.client_id);
          setStripe_connect_url(data.stripe_connect_url);
        }
      })
      .catch((error) => {
        //  console.log('error getting user profile => ', error);
      });
  };
  const getUserProfile = () => {
    const endpoint = 'user/profile';
    const method = 'GET';
    const headers = {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${userToken}`,
      Accept: 'application/json',
    };
    APICaller(endpoint, method, null, headers)
      .then((response) => {
        console.log('response getting user profile => ', response['data']);
        const {status, statusCode, message, data} = response['data'];
        if (status === 'success') {
          setstripe_connect_id(data.stripe_connect_id)
        //  console.log(data.stripe_connect_id)
        //  console.log('sds',data)
          dispatch(saveUserProfile(data));
        }
      })
      .catch((error) => {
        console.log('error getting user profile => ', error);
      });
  };
  const clientList = () => {
    const endpoint = 'user/clientList';
    const method = 'POST';
    const headers = {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${userToken}`,
      Accept: 'application/json',
    };
    APICaller(endpoint, method, null, headers)
      .then((response) => {
        console.log('=====> ', response['data']);
        const {status, statusCode, message, data} = response['data'];
        if (statusCode === 200) {
          console.log(message);
          setmarkers(message);
        }
      })
      .catch((error) => {
        console.log('error changing 121 => ', error['data']);
      });
  };
  const getaddress = async () => {
    if (userData && userData.latitude) {
      let respCurrent = await fetch(
        `https://maps.googleapis.com/maps/api/geocode/json?latlng=${userData.latitude},${userData.longitude}&key=AIzaSyBGwSKh1zM0HdzHuvZkZodwjNXJGafIjP4`,
      );
      let respJson = await respCurrent.json();
      setLocation(respJson.results[0].formatted_address);
    }
  };

  return (
    <View style={styles.container}>
      <Image
        source={constants.images.background}
        resizeMode={'stretch'}
        style={styles.containerBackground}
      />

      {/* Header */}
      <Header
        title={online_status == 1 ? 'Reaching out' : 'Find clients'}
        navigation={navigation}
        LeftComponent={() => (
          <TouchableOpacity onPress={() => navigation.navigate('MyProfile')}>
            <Image
              source={constants.images.ic_menu}
              style={{height: 18, width: 18, margin: 10}}
            />
          </TouchableOpacity>
        )}
      />

      {/* Address Input */}
      <View style={styles.LocationInput}>
        <View style={styles.locationInputBox}>
          <Image source={constants.images.ic_location} style={{margin: 10}} />
          {/* <TextInput
            // style={styles.addressInput}

          //  style={{ padding: 1,width:width * 0.8,textAlign:'center'}}
            style={{padding:10,width:width * 0.8,textAlign:'center',paddingBottom:5}}
            value={location}
            autoCapitalize={'none'}
           // editable={false}

          /> */}
          <Text style={{paddingLeft:8,paddingTop:5,width:width * 0.8,paddingBottom:5}}>{location}</Text>
        </View>
      </View>

      <View style={styles.middleView}>
        {/* <View style={styles.addressView}>
          <TextInput style={styles.textInput} placeholder={'ABC location'} />
        </View> */}

        <View style={styles.mapView}>
          <MapView
            style={{height: '100%', width: '100%', borderRadius: 10}}
            customMapStyle={mapStyle}
            mapPadding={{ left: 0, right: 0, top: 0, bottom: 60 }}
            // provider={PROVIDER_GOOGLE}
            initialRegion={{
              latitudeDelta: 0.0922,
              longitudeDelta: 0.0421,
              // latitude: 51.4684132,
              // longitude: -0.170704,
              latitude: userData && Number(userData.latitude),
              longitude: userData && Number(userData.longitude),
            }}>
            {markers &&
              markers.map((marker) => (
                <Marker
                  coordinate={marker.coordinates}
                  title={marker.title}
                  >
                  <Image
                    source={constants.images.Mapmarkerimg}
                    style={{width: 35, height: 50, zIndex: 11111}}
                  />
                </Marker>
              ))}
           
          </MapView>
        </View>
        <TouchableOpacity
          onPress={() => StatusChanged()}
          style={styles.bottonView}>
          <LinearGradient
            start={{x: 0, y: 1}}
            end={{x: 1, y: 1}}
            colors={['#228994', '#3BD8E5']}
            style={{
              justifyContent: 'center',
              alignItems: 'center',
              // height: Dimensions.get('window').height * 0.060,
              padding: 12,
              width: Dimensions.get('window').width * 0.9,
              borderRadius: 3,
            }}>
            {online_status == 1 ? (
              <Text style={styles.buttonTextSmall}>You're online now</Text>
            ) : (
              <View style={{justifyContent: 'center', alignItems: 'center'}}>
                <Text style={styles.buttonTextSmall}>
                  You're offline. Go online to
                </Text>
                <Text style={styles.buttonTextSmall}>find new clients</Text>
              </View>
            )}
          </LinearGradient>
        </TouchableOpacity>
      </View>
      <View style={styles.headerView} />
      {isModalVisible == true ? (
          <View
            style={{
              //flex: 1,
              // height: Dimensions.get('window').height,
              // width: Dimensions.get('window').width,
              backgroundColor: 'white',
            }}>
            <Modal animationType="fade" transparent={true} isVisible={true}>
              <View
                style={{
                  alignContent: 'center',
                  justifyContent: 'center',
                  backgroundColor: 'white',
                  /// marginHorizontal: 10,
                  borderRadius: 10,

                  marginTop: 10,
                  padding: 10,

                  // height:
                  //   Platform.OS == 'android'
                  //     ? Dimensions.get('window').height / 1.8
                  //     : Dimensions.get('window').height / 2.3,
                  // width: Dimensions.get('window').width,
                }}>
              
                <Text
                  style={{
                    textAlign: 'center',
                    fontSize: 18,
                    padding: 25,

                    color: constants.colors.fieldName,
                  }}>
                  Please ensure you have added your bank details in 'My Profile' to prevent any delays in paying you. Thank you!
                </Text>
                <TouchableOpacity
                  style={{
                    alignItems: 'center',
                    justifyContent: 'center',
                    // flex: 0.5,
                    // marginHorizontal: 20,
                    backgroundColor: '#228994',
                    padding: 10,
                    borderRadius: 5,
                    marginTop: 10,
                  }}
                  onPress={() => {
                    setModalVisible(false);
                    setlastModalVisible(1)
                  }}>
                  <Text
                    style={{
                      fontWeight: '500',
                      fontSize: 18,
                      color: constants.colors.white,
                    }}>
                    Ok
                  </Text>
                </TouchableOpacity>
              </View>
            </Modal>
          </View>
        ) : null}
      <AwesomeAlert
        show={showAlert}
        showProgress={false}
        message={
          online_status == 1
            ? "You've swiched to online mode"
            : "You've swiched to offline mode"
        }
        closeOnTouchOutside={true}
        showConfirmButton={true}
        confirmText="Confirm"
        confirmButtonColor={constants.colors.lightGreen}
        onCancelPressed={() => {
          setShowAlert(false);
          // navigation.navigate('MyProfile');
        }}
        onConfirmPressed={() => {
          setShowAlert(false);
          //  navigation.navigate('MyProfile');
        }}
        onDismiss={() => {
          setShowAlert(false);
          // navigation.navigate('MyProfile');
        }}
      />
        <AwesomeAlert
        show={showAlertPayment}
        showProgress={false}
        message={'Payment method not setup'}
        closeOnTouchOutside={true}
        showConfirmButton={true}
        confirmText="OK"
        confirmButtonColor={constants.colors.lightGreen}
        onCancelPressed={() => {
          setshowAlertPayment (false);
          //if (success) navigation.goBack();
        }}
        onConfirmPressed={() => {
          setshowAlertPayment(false);
          changeOnlineStatus()
         // if (success) navigation.goBack();
          // navigation.navigate('StripeConnect', {
          //     client_id: client_id,
          //     stripe_connect_url: stripe_connect_url,
          //   })
        }}
        onDismiss={() => {
          setShowAlert(false);
        }}
      />
      <AwesomeAlert
        show={showAlertMessage}
        showProgress={false}
        message={showmessage}
        closeOnTouchOutside={true}
        showConfirmButton={true}
        confirmText="Confirm"
        confirmButtonColor={constants.colors.lightGreen}
        onCancelPressed={() => {
          setshowAlertmessage (false);
          //if (success) navigation.goBack();
        }}
        onConfirmPressed={() => {
          setshowAlertmessage(false);
          navigation.navigate('StripeConnect', {
            client_id: client_id,
            stripe_connect_url: stripe_connect_url,
          })
         
        }}
        onDismiss={() => {
          setShowAlert(false);
        }}
      />
    </View>
  );
};

const mapStateToProps = (state) => ({
  userData: state.user.userData,
  userToken: state.user.userToken,
});

export default connect(mapStateToProps)(TherapistStatus);

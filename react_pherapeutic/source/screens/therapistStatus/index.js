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

  const {userToken, userData, navigation, dispatch} = props;
  const {online_status} = userData;

  useEffect(() => {
    console.log('online userData => ', userData);
    console.log('online status => ', online_status);
  }, [online_status]);

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
        }
      })
      .catch((error) => {
        console.log('error changing online status => ', error['data']);
      });
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
          <TextInput
            // style={styles.addressInput}
            style={{padding:5}}
            value={'London NW8 8QN, United Kingdom'}
            autoCapitalize={'none'}
          />
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
            // provider={PROVIDER_GOOGLE}
            initialRegion={{
              latitudeDelta: 0.0922,
              longitudeDelta: 0.0421,
              // latitude: 51.4684132,
              // longitude: -0.170704,
              latitude: userData && Number(userData.latitude),
              longitude: userData && Number(userData.longitude),
            }}
          />
        </View>
        <TouchableOpacity
          onPress={() => changeOnlineStatus()}
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
              <Text style={styles.buttonTextSmall}>YOU'RE ONLINE NOW</Text>
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
          navigation.navigate('MyProfile');
        }}
        onConfirmPressed={() => {
          setShowAlert(false);
          navigation.navigate('MyProfile');
        }}
        onDismiss={() => {
          setShowAlert(false);
          navigation.navigate('MyProfile');
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

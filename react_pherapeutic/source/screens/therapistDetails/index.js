import React, { useEffect, useState } from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  Image,
  TextInput,
  Button,
  Alert,
  Dimensions,
} from 'react-native';
import constants from '../../utils/constants';
import MapView, { PROVIDER_GOOGLE } from 'react-native-maps';
import { Rating } from 'react-native-ratings';
import CustomButton from '../../components/CustomButton';
import styles from './styles.js';
import { connect } from 'react-redux';
import APICaller from '../../utils/APICaller';
import { mapStyle } from '../therapistStatus';
import { Marker } from 'react-native-maps';
import Events from '../../utils/events';
import GestureRecognizer, { swipeDirections } from 'react-native-swipe-gestures';
import Header from '../../components/Header';
import GooglePlacesInput from '../../components/GooglePlacesInput';
import Geolocation from '@react-native-community/geolocation';
import Modal from 'react-native-modal';
import SubmitButton from '../../components/submitButton';
import LinearGradient from 'react-native-linear-gradient';
import { AirbnbRating } from 'react-native-ratings';
import StarRating from 'react-native-star-rating';
import { ScrollView } from 'react-native-gesture-handler';
import { color } from 'react-native-reanimated';
const { height, width } = Dimensions.get('window');
import AwesomeAlert from 'react-native-awesome-alerts';
const TherapistDetails = (props) => {
  const [therapistsList, setTherapistsList] = useState([]);
  const [isExpanded, setExpanded] = useState(false);
  const [location, setLocation] = useState('');
  const [location_map, setLocation_map] = useState([]);
  const [isModalVisible, setModalVisible] = useState(false);

  const [therapist, setTherapist] = useState(null);
  const [therapistNumber, setTherapistNumber] = useState(0);
  const { userToken, navigation, userData } = props;
  const [selectedCardDetails, setSelectedCardDetails] = useState('');
  const [showAlert, setShowAlert] = useState(false);
  const [alertMessage, setAlert] = useState('');

  useEffect(() => {
    getCurrentCoordinates_map();
    Geolocation.getCurrentPosition((info) => setLocation_map(info));
    Geolocation.getCurrentPosition((info) => getaddress(info));
    if (!therapist) getTherapistsList();
    getCurrentCoordinates();
  }, [therapist]);
  const getCurrentCoordinates = async () => {
    await Geolocation.getCurrentPosition((info) => getaddress(info));
  };
  const getCurrentCoordinates_map = async () => {
    await Geolocation.getCurrentPosition((info) => setLocation_map(info));
  };

  const getaddress = async (info) => {
    if (info && info.coords) {
      let respCurrent = await fetch(
        `https://maps.googleapis.com/maps/api/geocode/json?latlng=${info.coords.latitude},${info.coords.longitude}&key=AIzaSyBGwSKh1zM0HdzHuvZkZodwjNXJGafIjP4`,
      );
      console.log(respCurrent)
      let respJson = await respCurrent.json();
      console.log(respJson)
      setLocation(respJson.results[0].formatted_address);
    }
  };
  //   const getaddress = async () => {
  //     console.log('userData'+JSON.stringify(userData))
  // if (userData && userData.latitude) {
  //     let respCurrent = await fetch(`https://maps.googleapis.com/maps/api/geocode/json?latlng=${userData.latitude},${userData.longitude}&key=AIzaSyBGwSKh1zM0HdzHuvZkZodwjNXJGafIjP4`);
  //     let respJson = await respCurrent.json();
  //     setLocation(respJson.results[0].formatted_address)
  //   }
  // };

  const createCall = (therapistId,selectedCardDetailsdata) => {
      const endpoint = 'createCall';
      const method = 'POST';
      const headers = {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${userToken}`,
        Accept: 'application/json',
      };
      var body = {
        card_id: selectedCardDetailsdata.id,
        therapist_id: therapistId,
      };
      console.log('data before sending => ', body);
      // alert(JSON.stringify(body));
      APICaller(endpoint, method, body, headers)
        .then((response) => {

          console.log('response after creating Call => ', response['data']);
          const {status, statusCode, message, data} = response['data'];
          if (statusCode === 200) {
            callTherapist_call(data,therapistId);
          } else {
            setAlert(message);
            setShowAlert(true);
          }
          // alert(JSON.stringify(response));
        })
        .catch((error) => {
          // alert(JSON.stringify(error));
      
          console.log('error updating user profile => ', error['data']);
          const {status, statusCode, message, data} = error['data'];
          setAlert(message);
          setShowAlert(true);
        });
   
  };
  const callTherapist_call = (data,therapistId) => {
    // alert(JSON.stringify(therapistDetail));
    console.log('therapist details => ', therapistId);

    let channelname = 'channel_' + Date.now();
    const endpoint =
      'sendVideoCallNotificationToTherapist/' +
      therapistId +
      '?channel_name=' +
      channelname;
    const method = 'GET';
    const headers = {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${userToken}`,
      Accept: 'application/json',
    };
    APICaller(endpoint, method, null, headers)
      .then((response) => {
        //   console.log('userToken'+userToken)
        console.log('response calling push => ', response);
        navigation.navigate('VideoCall', {
          CallReciverName:therapist.first_name,
          channelnamedata: channelname,
          callDetails: data,
        });
      })
      .catch((error) => {
        console.log('error calling therapist => ', error);
      });
  };
  
  const callTherapist = (user_id, fullname, therapist) => {

    //Get card Data//
    const endpoint = 'getUserCards';
    const method = 'GET';
    const headers = {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${userToken}`,
      Accept: 'application/json',
    };
    APICaller(endpoint, method, null, headers)
      .then((response) => {
        const { status, statusCode, message, data } = response['data'];

        // if (data && data.length > 0) {
        // //  setSelectedCardDetails(data[0]);
        //   createCall(user_id,data[0])
        // }
        // else{
  navigation.navigate('selectPaymentMethod', {
      therapistId: user_id,
      therapist: therapist,
    });
      //  }

     
      })
      .catch((error) => {
        console.log('error getting user card details => ', error);
      });


    //end Getcard//

  
  };

  const getTherapistsList = () => {
    let Gpsdata = Geolocation.getCurrentPosition((info) => info);
    console.log('Gpsdata' + JSON.stringify(Gpsdata));
    var latitude = '';
    var longitude = '';
    console.log('location_map' + location_map);
    if (location_map && location_map.coords) {
      latitude = location_map.coords.latitude;
      longitude = location_map.coords.longitude;
    }
    const endpoint = 'user/search/therapistlist';
    const method = 'POST';
    const headers = {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${userToken}`,
      Accept: 'application/json',
    };
    let body = new FormData();

    body.append('latitude', latitude);
    body.append('longitude', longitude);

    body.append('default', 1);
    console.log('body' + JSON.stringify(body));
    APICaller(endpoint, method, body, headers)
      .then((response) => {
        console.log(
          '11response getting therapist list => ',
          response['data'],
          '------',
        );
        const { status, statusCode, message, data } = response['data'];

        if (data && data.length > 0) {
          setTherapistsList(data);
          setTherapist({
            first_name: data[0].first_name,
            last_name: data[0].last_name,
            experience: data[0].experience,
            qualification: data[0].qualification,
            Languages: data[0].Languages,
            Specialism: data[0].Specialism,
            amount: data[0].amount,
            user_id: data[0].user_id,
            latitude: data[0].latitude,
            longitude: data[0].longitude,
            rating: data[0].rating,
            image: data[0].image,
            consultations_count: data[0].consultations_count,
          });
        } else {
          setModalVisible(true);
        }
      })
      .catch((error) => {
        //alert(error);
        console.log('response getting therapist listtt => ', error['data']);
      });
  };
  const getTherapistsListDefalut = () => {
    setModalVisible(false);
    var latitude = '';
    var longitude = '';

    if (location_map && location_map.coords) {
      latitude = location_map.coords.latitude;
      longitude = location_map.coords.longitude;
    }
    const endpoint = 'user/search/therapistlist';
    const method = 'POST';
    const headers = {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${userToken}`,
      Accept: 'application/json',
    };
    let body = new FormData();
    //  body.append('latitude', '30.7046');
    //  body.append('longitude', '76.7179');
    body.append('latitude', latitude);
    body.append('longitude', longitude);
    body.append('default', 2);

    APICaller(endpoint, method, body, headers)
      .then((response) => {
        console.log(
          '22211response getting therapist list => ',
          response['data'],
          '------',
        );
        const { status, statusCode, message, data } = response['data'];
        if (data && data.length > 0) {
          setTherapistsList(data);
          setTherapist({
            first_name: data[0].first_name,
            last_name: data[0].last_name,
            experience: data[0].experience,
            qualification: data[0].qualification,
            Languages: data[0].Languages,
            Specialism: data[0].Specialism,
            amount: data[0].amount,
            user_id: data[0].user_id,
            latitude: data[0].latitude,
            longitude: data[0].longitude,
            rating: data[0].rating,
            image: data[0].image,
            consultations_count: data[0].consultations_count,
          });
        } else {
        }
      })
      .catch((error) => {
        console.log('response getting therapist listtt => ', error['data']);
      });
  };

  onSwipeUp = (gestureState) => {
    if (!isExpanded) {
      setExpanded(true);
    }
  };

  onSwipeDown = (gestureState) => {
    if (isExpanded) {
      setExpanded(false);
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
        title={`Find a verified ${'\n'}therapist now`}
        navigation={navigation}
        LeftComponent={() => (
          <TouchableOpacity onPress={() => navigation.navigate('MyProfile')}>
            <Image
              source={constants.images.ic_menu}
              style={{ height: 18, width: 18, margin: 10 }}
            />
          </TouchableOpacity>
        )}
      />

      {/* Address Input */}
      <View style={styles.LocationInput}>
        <View style={styles.locationInputBox}>
          <Image source={constants.images.ic_location} style={{ margin: 10 }} />
          {/* <TextInput
            //style={styles.addressInput}
            style={{ padding: 1, width: width * 0.8, textAlign: 'center' }}
            onChangeText={(text) => setLocation(text)}
            value={location}
          /> */}
          <Text style={{ paddingLeft: 8, paddingTop: 5, width: width * 0.8, paddingBottom: 5 }}>{location}</Text>
          {/* <GooglePlacesInput
           
            onPress={(data) => {
              setLocation(data.description);
            }}
            onSetLocation={(ref) => {
              ref && ref.setAddressText(location);
            }}
            value={location}
            placeholder="Current location"
            text="Location"
          />  */}
        </View>
      </View>
      <Modal isVisible={isModalVisible}>
        <View style={{ backgroundColor: 'white', borderRadius: 10, padding: 15 }}>
          <Image
            style={{ alignSelf: 'center' }}
            source={constants.images.shutterstock}
          />
          <View style={{ padding: 10 }}>
            <Text style={styles.therapistmessage}>
              So sorry. We are brand new and experiencing issues connecting you.
              We want to help so please bear with us. If you don't want to wait,
              we can connect you to any available therapist
            </Text>
          </View>
          <View
            style={{
              flexDirection: 'row',
              justifyContent: 'space-around',
              marginTop: 10,
              padding: 10,
            }}>
            <TouchableOpacity
              onPress={() => getTherapistsListDefalut()}
              style={{ justifyContent: 'center', alignItems: 'center' }}>
              <LinearGradient
                start={{ x: 0, y: 1 }}
                end={{ x: 1, y: 1 }}
                colors={['#228994', '#3BD8E5']}
                style={{
                  justifyContent: 'center',
                  alignItems: 'center',
                  // height: Dimensions.get('window').height * 0.032,
                  // width: Dimensions.get('window').width * 0.15,
                  padding: 10,
                  borderRadius: 3,
                }}>
                <Text
                  style={{
                    color: constants.colors.white,
                    fontWeight: '500',
                    fontSize: 13,
                    textAlign: 'center',
                  }}>
                  {'Yes, connect me \n right away'}
                </Text>
              </LinearGradient>
            </TouchableOpacity>
            <TouchableOpacity
              onPress={() => setModalVisible(false)}
              style={{
                borderRadius: 3,
                padding: 10,
                justifyContent: 'center',
                alignItems: 'center',
                borderColor: '#0F919F',
                borderWidth: 1,
              }}>
              <Text
                style={{
                  color: constants.colors.fieldName,
                  fontWeight: '500',
                  fontSize: 13,
                  textAlign: 'center',
                }}>
                {'No, i’d rather \n wait'}
              </Text>
            </TouchableOpacity>
          </View>
        </View>
      </Modal>
      {therapist ? (

        <View style={styles.mapScreen}>
          <View style={[styles.mapView, { flex: 1 }]}>
            {therapist.latitude != null && (
              <MapView
                style={styles.map}
                mapPadding={{ left: 0, right: 0, top: 0, bottom: 60 }}
                customMapStyle={mapStyle}
                region={{
                  // latitude: therapist.latitude?therapist.latitude:0,
                  // longitude: therapist.longitude?therapist.longitude:0,
                  latitude: therapist && Number(therapist.latitude),
                  longitude: therapist && Number(therapist.longitude),
                  latitudeDelta: 0,
                  longitudeDelta: 0.0121,
                }}>
                <Marker
                  coordinate={{
                    // latitude: therapist.latitude?therapist.latitude:0,
                    // longitude: therapist.longitude?therapist.longitude:0,

                    latitude: therapist && Number(therapist.latitude),
                    longitude: therapist && Number(therapist.longitude),
                  }}
                />
              </MapView>
            )}
          </View>
          <GestureRecognizer
            onSwipeUp={(state) => this.onSwipeUp(state)}
            onSwipeDown={(state) => this.onSwipeDown(state)}>
            <View style={styles.swipeAction}>
              <Image
                source={
                  isExpanded
                    ? constants.images.upArrow
                    : constants.images.downArrow
                }
              />
              <Text style={styles.swipeTextProps}>
                {isExpanded ? '' : ' Swipe up for more information'}
              </Text>
            </View>

            <View style={styles.therapistInfo}>
              <View
                style={{
                  justifyContent: 'space-around',
                  alignItems: 'center',
                }}>
                <Image
                  source={
                    therapist && therapist.image !== null
                      ? { uri: therapist.image }
                      : constants.images.defaultUserImage
                  }
                  style={{ width: 80, height: 100, borderRadius: 10 }}
                />
                {/* <Text style={styles.price}>Cost: £50/-</Text> */}
                <Text style={styles.price}>Cost: £{therapist.amount}/-</Text>
              </View>

              <View
                style={{
                  flex: 2,
                  marginLeft: 10,
                  justifyContent: 'space-around',
                  alignItems: 'flex-start',
                }}>
                <View style={{ flexDirection: 'row', width: '100%' }}>
                  <View style={([styles.name], { flex: 1 })}>
                    <Text style={{ fontSize: 16 }}>
                      {therapist.first_name} {therapist.last_name}
                    </Text>
                  </View>
                  <View
                    style={{
                      height: 25,
                      padding: 5,
                      flexDirection: 'row',
                      borderColor: 'rgb(24,173,189)',
                      borderRadius: 10,
                      backgroundColor: 'rgb(24,173,189)',
                    }}>
                    {/* <Text
                      style={{
                        fontSize: 14,
                        color: 'white',
                        textAlign: 'left',
                        paddingLeft: '2%',
                        alignSelf: 'center',
                      }}>
                      {therapist.rating}
                    </Text> */}

                    {/* <AirbnbRating
                      showRating={false}
                      isDisabled={true}
                      count={5}
                      size={10}
                      defaultRating={parseInt(therapist.rating)}
                    //defaultRating={0}
                      selectedColor="white"
                      ratingColor="#3498db"
                      ratingBackgroundColor="rgb(24,173,189)"
                      starContainerStyle="white"
                    />   */}

                    <StarRating
                      starSize={15}
                      disabled={true}
                      maxStars={5}
                      rating={parseInt(therapist.rating)}
                      starStyle={{ color: 'white' }}

                    //selectedStar={(rating) => this.onStarRatingPress(rating)}
                    />

                  </View>
                </View>
                <Text style={{ fontSize: 14 }}>
                  {therapist.experience ? therapist.experience : 0} years experience
                </Text>
                <Text>{therapist.consultations_count} consultations done</Text>
              </View>
            </View>
          </GestureRecognizer>

          {isExpanded && (
            <View style={styles.info}>
              <View style={styles.infoDetails}>
                <Text style={styles.infoHeading}>Qualification:</Text>
                <Text style={styles.infoDesc}> {therapist.qualification}</Text>
              </View>
              <View style={styles.infoDetails}>
                <Text style={styles.infoHeading}>Languages:</Text>
                <Text style={styles.infoDesc}>{therapist.Languages} </Text>
              </View>
              <View style={styles.infoDetails}>
                <Text style={styles.infoHeading}>Specialism:</Text>
                <View style={{ width: "75%" }}>
                  <Text style={styles.infoDesc}>{therapist.Specialism}</Text>
                </View>
              </View>
            </View>
          )}

          <View style={styles.callButton}>
            <CustomButton
              title={'CALL NOW'}
              submitFunction={() => {
                // alert(JSON.stringify(therapist));
                callTherapist(
                  therapist.user_id,
                  therapist.first_name + therapist.last_name,
                  therapist,
                );
              }}
            />
          </View>
          <View style={styles.misc}>
            <TouchableOpacity
              onPress={() => {
                console.log('[[>>> ', therapistsList.length);
                console.log('thh ', therapistsList);
                console.log('[[>>> ', therapistNumber);
                if (therapistNumber < therapistsList.length - 1) {
                  Events.trigger('showModalLoader');
                  setTimeout(() => {
                    setTherapist({
                      first_name:
                        therapistsList[therapistNumber + 1].first_name,
                      last_name: therapistsList[therapistNumber + 1].last_name,
                      experience:
                        therapistsList[therapistNumber + 1].experience,
                      qualification:
                        therapistsList[therapistNumber + 1].qualification,
                      Languages: therapistsList[therapistNumber + 1].Languages,
                      Specialism:
                        therapistsList[therapistNumber + 1].Specialism,
                      amount: therapistsList[therapistNumber + 1].amount,
                      user_id: therapistsList[therapistNumber + 1].user_id,
                      latitude: therapistsList[therapistNumber + 1].latitude,
                      longitude: therapistsList[therapistNumber + 1].longitude,
                      rating: therapistsList[therapistNumber + 1].rating,
                      image: therapistsList[therapistNumber + 1].image,
                      consultations_count:
                        therapistsList[therapistNumber + 1].consultations_count,
                    });

                    setTherapistNumber((prevNo) => ++prevNo);
                    Events.trigger('hideModalLoader');
                  }, 500);
                }
              }}
              style={{ justifyContent: 'center', alignItems: 'center' }}>
              <Text style={styles.textStylesOne}>Search Again</Text>
            </TouchableOpacity>
            <TouchableOpacity
              onPress={() => navigation.goBack()}
              style={{ justifyContent: 'center', alignItems: 'center' }}>
              <Text style={styles.textStylesTwo}>Cancel</Text>
            </TouchableOpacity>
          </View>
        </View>

      ) : (
        <View style={styles.mapScreen}></View>
      )}
         <AwesomeAlert
        show={showAlert}
        showProgress={false}
        message={alertMessage}
        closeOnTouchOutside={true}
        showConfirmButton={true}
        confirmText="Confirm"
        confirmButtonColor={constants.colors.lightGreen}
        onCancelPressed={() => {
          setShowAlert(false);
        }}
        onConfirmPressed={() => {
         
          setShowAlert(false); 
        }}
        onDismiss={() => {
          setShowAlert(false);
        }}
      />
    </View>
  );
};

const mapStateToProps = (state) => ({
  userToken: state.user.userToken,
  userData: state.user.userData,
});

export default connect(mapStateToProps)(TherapistDetails);

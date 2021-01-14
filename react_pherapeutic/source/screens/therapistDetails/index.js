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
import Modal from 'react-native-modal';
import SubmitButton from '../../components/submitButton';
import LinearGradient from 'react-native-linear-gradient';
import { AirbnbRating } from 'react-native-ratings';
const { height, width } = Dimensions.get('window');
const TherapistDetails = (props) => {
  const [therapistsList, setTherapistsList] = useState([]);
  const [isExpanded, setExpanded] = useState(false);
  const [location, setLocation] = useState('London NW8 8QN, United Kingdom');
  const [isModalVisible, setModalVisible] = useState(false);

  const [therapist, setTherapist] = useState(null);
  const [therapistNumber, setTherapistNumber] = useState(0);
  const { userToken, navigation } = props;

  useEffect(() => {
    if (!therapist) getTherapistsList();
    // if (!therapist.image) getTherapistsList();
  }, [therapist]);

  const callTherapist = (user_id, fullname, therapist) => {
    navigation.navigate('selectPaymentMethod', {
      therapistId: user_id,
      therapist: therapist,
    });

    // let channelname = 'channel_' + Date.now();
    // const endpoint =
    //   'sendVideoCallNotificationToTherapist/' +
    //   user_id +
    //   '?channel_name=' +
    //   channelname;
    // const method = 'GET';
    // const headers = {
    //   'Content-Type': 'application/json',
    //   Authorization: `Bearer ${userToken}`,
    //   Accept: 'application/json',
    // };
    // APICaller(endpoint, method, null, headers)
    //   .then((response) => {
    //     //   console.log('userToken'+userToken)
    //     //   console.log("response calling therapist => ", response)
    //     navigation.navigate('VideoCall', {
    //       CallReciverName: fullname,
    //       channelnamedata: channelname,
    //     });
    //   })
    //   .catch((error) => {
    //     console.log('error calling therapist => ', error);
    //   });
  };

  // console.log("therapist data ===> ", therapist)
  const getTherapistsList = () => {
    //  const endpoint = 'user/search/therapist';
    const endpoint = 'user/search/therapistlist';
    const method = 'POST';
    const headers = {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${userToken}`,
      Accept: 'application/json',
    };
    let body = new FormData();
    body.append('latitude', '30.7046');
    body.append('longitude', '76.7179');
    body.append('default', 1);
    APICaller(endpoint, method, body, headers)
      .then((response) => {
        console.log(
          '11response getting therapist list => ',
          response['data'],
          '------',
        );
        const { status, statusCode, message, data } = response['data'];
        // alert(JSON.stringify(data.length));

        if (data && data.length > 0) {
          setTherapistsList(data);

          setTherapist({
            image:
              'https://firebasestorage.googleapis.com/v0/b/ihad2lie.appspot.com/o/profileImages%2F1603133558?alt=media&token=261fe67f-bf96-461f-8efc-b15c44abe9a5',
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
            rating: data[0].rating


          });
        } else {
          setModalVisible(true)

          // alert('No Therapist Found');

          //   <AwesomeAlert
          //   show={showAlert}
          //   showProgress={false}
          //   message={message}
          //   closeOnTouchOutside={true}
          //   showConfirmButton={true}
          //   confirmText="Confirm"
          //   confirmButtonColor={constants.colors.lightGreen}
          //   onCancelPressed={() => {
          //     setShowAlert(false);
          //     if (success) navigation.goBack();
          //   }}
          //   onConfirmPressed={() => {
          //     setShowAlert(false);
          //     if (success) navigation.goBack();
          //   }}
          //   onDismiss={() => {
          //     setShowAlert(false);
          //     if (success) navigation.goBack();
          //   }}
          // />

        }
        // setTherapistsList([
        //   {
        //     image:
        //       'https://firebasestorage.googleapis.com/v0/b/ihad2lie.appspot.com/o/profileImages%2F1603133558?alt=media&token=261fe67f-bf96-461f-8efc-b15c44abe9a5',
        //     first_name: 'Avan',
        //     last_name: 'saam',
        //     experience: 3,
        //     qualification: 'MBBS',
        //     language: 'English',
        //     specialism: 'andf',
        //   },
        //   {
        //     image:
        //       'https://firebasestorage.googleapis.com/v0/b/ihad2lie.appspot.com/o/profileImages%2F288C4549F0B6.jpg?alt=media&token=76a97901-f328-4a04-aa76-58f4d9e243cb',
        //     first_name: 'Akin',
        //     last_name: 'see',
        //     experience: 12,
        //     qualification: 'graduation',
        //     language: 'French',
        //     specialism: 'Depression',
        //   },
        //   {
        //     image:
        //       'https://firebasestorage.googleapis.com/v0/b/ihad2lie.appspot.com/o/profileImages%2F1603763121?alt=media&token=762e32c9-64ca-44fa-8bb3-1c2604ed4222',
        //     first_name: 'Akin',
        //     last_name: 'see',
        //     experience: 12,
        //     qualification: 'graduation',
        //     language: 'English',
        //     specialism: 'Depression, Anger',
        //   },
        // ]);
      })
      .catch((error) => {
        //alert(error);
        console.log('response getting therapist listtt => ', error['data']);
      });
  };

  const getTherapistsListDefalut = () => {
    setModalVisible(false)
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
    body.append('latitude', '24.2993434');
    body.append('longitude', '75.2062532');
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
          // setTherapistsList(data);

          setTherapist({
            image:
              'https://firebasestorage.googleapis.com/v0/b/ihad2lie.appspot.com/o/profileImages%2F1603133558?alt=media&token=261fe67f-bf96-461f-8efc-b15c44abe9a5',
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
            rating: data[0].rating

          });
        } else {
          // setModalVisible(true)
        }

      })
      .catch((error) => {
        //   alert(error);
        console.log('response getting therapist listtt => ', error['data']);
      });
  };

  onSwipeUp = (gestureState) => {
    // this.setState({ myText: 'You swiped up!' });
    // alert('Called');
    if (!isExpanded) {
      setExpanded(true);
    }
    // navigation.navigate('MapDetailScreen');
  };

  onSwipeDown = (gestureState) => {
    // this.setState({ myText: 'You swiped up!' });
    // alert('Called');
    if (isExpanded) {
      setExpanded(false);
    }
    // navigation.navigate('MapDetailScreen');
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
          <TextInput
            //style={styles.addressInput}
            style={{ padding: 1 }}
            onChangeText={(text) => setLocation(text)}
            value={location}
          />
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
          /> */}
        </View>
      </View>
      <Modal isVisible={isModalVisible}>
        <View style={{ backgroundColor: 'white', borderRadius: 5, padding: 10 }}>
          <Image style={{ alignSelf: 'center' }} source={constants.images.shutterstock} />
          <View style={{ padding: 5 }}>
            <Text style={styles.therapistmessage}>“One moment, we’ll connect you {'\n'} soon. If you don’t want to wait, we  {'\n'}can connect you to any available  {'\n'} therapist”</Text>
          </View>
          <View style={{ flexDirection: 'row', justifyContent: 'space-around', marginTop: 10, padding: 10 }}>
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
                    textAlign: 'center'
                  }}>
                  {'Yes, connect me \n right away'}
                </Text>
              </LinearGradient>
            </TouchableOpacity>
            <TouchableOpacity
              onPress={() => setModalVisible(false)}
              style={{ borderRadius: 3, padding: 10, justifyContent: 'center', alignItems: 'center', borderColor: '#0F919F', borderWidth: 1 }}>

              <Text
                style={{
                  color: constants.colors.fieldName,
                  fontWeight: '500',
                  fontSize: 13,
                  textAlign: 'center'
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
            {therapist.latitude != null &&

              <MapView
                style={styles.map}
                customMapStyle={mapStyle}
                region={{
                  //     latitude: therapist.latitude?therapist.latitude:0,
                  // longitude: therapist.longitude?therapist.longitude:0,
                  latitude: therapist && Number(therapist.latitude),
                  longitude: therapist && Number(therapist.longitude),
                  //  latitude: 22.719568,
                  //  longitude: 75.857727,

                  latitudeDelta: 0,
                  longitudeDelta: 0.0121,
                }}>
                <Marker
                  coordinate={{
                    // latitude: therapist.latitude?therapist.latitude:0,
                    // longitude: therapist.longitude?therapist.longitude:0,
                    //  latitude: 22.719568,
                    //  longitude: 75.857727,
                    latitude: therapist && Number(therapist.latitude),
                    longitude: therapist && Number(therapist.longitude),
                    // latitude: 37.78825,
                    // longitude: -122.4324,
                  }}
                />
              </MapView>
            }
          </View>
          <GestureRecognizer
            style={{}}
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
                    therapist && therapist['image'] !== null
                      ? { uri: therapist['image'] }
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


                  <View style={[styles.name], { flex: 1 }}>
                    <Text style={{ fontSize: 16 }}>
                      {therapist.first_name} {therapist.last_name}
                    </Text>
                  </View>
                  <View style={{ height: 25, padding: 5, flexDirection: "row", borderColor: "rgb(24,173,189)", borderRadius: 10, backgroundColor: "rgb(24,173,189)" }}>

                    <Text style={{ fontSize: 14, color: "white", textAlign: "left", paddingLeft: "2%", alignSelf: "center" }}>{therapist.rating}</Text>

                    <AirbnbRating
                      showRating={false}
                      isDisabled={true}
                      count={5}
                      size={10}
                      defaultRating={therapist.rating}

                      selectedColor="white"
                      ratingColor='#3498db'
                      ratingBackgroundColor="rgb(24,173,189)"
                      starContainerStyle="white"

                    />
                  </View>
                </View>
                <Text style={{ fontSize: 14 }}>
                  {therapist.experience} years experience
                </Text>
                <Text>12 consultations done</Text>
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
                <Text style={styles.infoDesc}>{therapist.Specialism}</Text>
              </View>
            </View>
          )}

          <View style={styles.callButton}>
            <CustomButton
              title={'Call Now'}
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
                    //  setTherapist(therapistsList[therapistNumber + 1]);
                    setTherapist({
                      image:
                        'https://firebasestorage.googleapis.com/v0/b/ihad2lie.appspot.com/o/profileImages%2F1603133558?alt=media&token=261fe67f-bf96-461f-8efc-b15c44abe9a5',
                      first_name:
                        therapistsList[therapistNumber + 1].first_name,
                      last_name: therapistsList[therapistNumber + 1].last_name,
                      experience:therapistsList[therapistNumber + 1].experience,
                      qualification:therapistsList[therapistNumber + 1].qualification,
                      Languages:  therapistsList[therapistNumber + 1].Languages,
                      Specialism: therapistsList[therapistNumber + 1].Specialism,
                      amount: therapistsList[therapistNumber + 1].amount,
                      user_id: therapistsList[therapistNumber + 1].user_id,
                      latitude: therapistsList[therapistNumber + 1].latitude,
                      longitude: therapistsList[therapistNumber + 1].longitude,
                      rating: therapistsList[therapistNumber + 1].rating
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
          <View style={styles.mapScreen}>
            {/* <Text style={styles.textStylesOne}>Search Again</Text> */}
          </View>
        )}
    </View>
  );
};

const mapStateToProps = (state) => ({
  userToken: state.user.userToken,
});

export default connect(mapStateToProps)(TherapistDetails);

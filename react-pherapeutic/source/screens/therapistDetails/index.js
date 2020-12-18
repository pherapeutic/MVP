import React, { useEffect, useState } from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  Image,
  TextInput,
  Button,
  Alert,
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

const TherapistDetails = (props) => {
  const [therapistsList, setTherapistsList] = useState([]);
  const [isExpanded, setExpanded] = useState(false);
  const [location, setLocation] = useState('London NW8 8QN, United Kingdom');

  const [therapist, setTherapist] = useState({
    image: null,
    first_name: null,
    last_name: null,
    experience: null,
    qualification: null,
  });
  const [therapistNumber, setTherapistNumber] = useState(0);
  const { userToken, navigation } = props;

  useEffect(() => {
    // if (!therapist)
    //    getTherapistsList()
    if (!therapist.image) getTherapistsList();
  }, [therapist]);

  const callTherapist = () => {
    const endpoint = "sendVideoCallNotificationToTherapist/32";
    const method = "GET";
    const headers = {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${userToken}`,
      Accept: 'application/json',
    };
    APICaller(endpoint, method, null, headers)
      .then(response => {
        console.log("response calling therapist => ", response)
      })
      .catch(error => {
        console.log("error calling therapist => ", error)
      })

  }

  // console.log("therapist data ===> ", therapist)
  const getTherapistsList = () => {
    const endpoint = 'user/search/therapist';
    const method = 'POST';
    const headers = {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${userToken}`,
      Accept: 'application/json',
    };
    let body = new FormData();
    body.append('latitude', '30.7046');
    body.append('longitude', '76.7179');
    APICaller(endpoint, method, body, headers)
      .then((response) => {
        console.log(
          'response getting therapist list => ',
          response['data'],
          '------',
        );
        const { status, statusCode, message, data } = response['data'];

        setTherapistsList([
          {
            image:
              'https://firebasestorage.googleapis.com/v0/b/ihad2lie.appspot.com/o/profileImages%2F1603133558?alt=media&token=261fe67f-bf96-461f-8efc-b15c44abe9a5',
            first_name: 'Avan',
            last_name: 'saam',
            experience: 3,
            qualification: 'MBBS',
            language: 'English',
            specialism: 'andf',
          },
          {
            image:
              'https://firebasestorage.googleapis.com/v0/b/ihad2lie.appspot.com/o/profileImages%2F288C4549F0B6.jpg?alt=media&token=76a97901-f328-4a04-aa76-58f4d9e243cb',
            first_name: 'Akin',
            last_name: 'see',
            experience: 12,
            qualification: 'graduation',
            language: 'French',
            specialism: 'Depression',
          },
          {
            image:
              'https://firebasestorage.googleapis.com/v0/b/ihad2lie.appspot.com/o/profileImages%2F1603763121?alt=media&token=762e32c9-64ca-44fa-8bb3-1c2604ed4222',
            first_name: 'Akin',
            last_name: 'see',
            experience: 12,
            qualification: 'graduation',
            language: 'English',
            specialism: 'Depression, Anger',
          },
        ]);
        setTherapist({
          image:
            'https://firebasestorage.googleapis.com/v0/b/ihad2lie.appspot.com/o/profileImages%2F1603133558?alt=media&token=261fe67f-bf96-461f-8efc-b15c44abe9a5',
          first_name: 'Avan',
          last_name: 'saam',
          experience: 3,
          qualification: 'MBBS',
          language: 'English',
          specialism: 'sjhfgh',
        });
      })
      .catch((error) => {
        alert(error);
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
            // style={styles.addressInput}
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

      <View style={styles.mapScreen}>
        <View style={[styles.mapView, { flex: 1 }]}>
          <MapView
            style={styles.map}
            customMapStyle={mapStyle}
            region={{
              latitude: 37.78825,
              longitude: -122.4324,
              latitudeDelta: 0,
              longitudeDelta: 0.0121,
            }}>
            <Marker
              coordinate={{
                latitude: 37.78825,
                longitude: -122.4324,
              }}
            />
          </MapView>
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
              {isExpanded ? ' Swipe down' : ' Swipe up for more information'}
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
                style={{ width: 90, height: 110, borderRadius: 10 }}
              />
              <Text style={styles.price}>Cost: £50/-</Text>
            </View>

            <View
              style={{
                flex: 2,
                marginLeft: 10,
                justifyContent: 'space-around',
                alignItems: 'flex-start',
              }}>
              <View style={styles.name}>
                <Text style={{ fontSize: 16 }}>DR. MARIE SMITH</Text>
              </View>
              <Text style={{ fontSize: 14 }}>20 years experience</Text>
              <Text>12 consultations done</Text>
            </View>
          </View>
        </GestureRecognizer>

        {isExpanded && (
          <View style={styles.info}>
            <View style={styles.infoDetails}>
              <Text style={styles.infoHeading}>Qualification:</Text>
              <Text style={styles.infoDesc}>PhD in Psychology</Text>
            </View>
            <View style={styles.infoDetails}>
              <Text style={styles.infoHeading}>Languages:</Text>
              <Text style={styles.infoDesc}>English, Italian, Spanish</Text>
            </View>
            <View style={styles.infoDetails}>
              <Text style={styles.infoHeading}>Specialism:</Text>
              <Text style={styles.infoDesc}>Psychotherapy, Cognitive</Text>
            </View>
          </View>
        )}

        <View style={styles.callButton}>
          <CustomButton
            title={'Call Now'}
            submitFunction={() => callTherapist()}
          />
        </View>
        <View style={styles.misc}>
          <TouchableOpacity
            onPress={() => {
              console.log('[[>>> ', therapistsList.length);
              console.log('[[>>> ', therapistNumber);
              if (therapistNumber < therapistsList.length - 1) {
                Events.trigger('showModalLoader');
                setTimeout(() => {
                  setTherapist(therapistsList[therapistNumber + 1]);
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
    </View>
  );
};

const mapStateToProps = (state) => ({
  userToken: state.user.userToken,
});

export default connect(mapStateToProps)(TherapistDetails);

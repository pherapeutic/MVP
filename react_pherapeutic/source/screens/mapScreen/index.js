import React, {useState} from 'react';
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
import styles from './styles';
import MapView, {PROVIDER_GOOGLE} from 'react-native-maps';
import {Rating} from 'react-native-ratings';
import CustomButton from '../../components/CustomButton';
import GestureRecognizer, {swipeDirections} from 'react-native-swipe-gestures';

const MapScreen = (props) => {
  onSwipeUp = (gestureState) => {
    // this.setState({ myText: 'You swiped up!' });
    // alert('Called');
    navigation.navigate('MapDetailScreen');
  };

  const {navigation} = props;
  return (
    <GestureRecognizer
      style={{flex: 1}}
      onSwipeUp={(state) => this.onSwipeUp(state)}>
      <View style={styles.container}>
        <Image
          source={constants.images.background}
          resizeMode={'stretch'}
          style={styles.containerBackground}
        />
        <View style={styles.containerView}>
          {/* Header */}
          <View style={styles.header}>
            <View style={{flex: 2}}>
              <TouchableOpacity onPress={() => navigation.goBack()}>
                <Image
                  source={constants.images.backArrowWhite}
                  style={{height: 18, width: 18, margin: 10}}
                />
              </TouchableOpacity>
            </View>
            <View style={{flex: 3}}>
              <Text style={[styles.headingText]}>
                Find a verified {'\n'}
                therapist now
              </Text>
            </View>
            <View style={{flex: 1}}></View>
          </View>

          {/* Address Input */}
          <View style={styles.LocationInput}>
            <View style={styles.locationInputBox}>
              <Image
                source={constants.images.ic_location}
                style={{height: 27, width: 18, margin: 12}}
              />
              <TextInput
                // style={styles.addressInput}
                value={'London NW8 8QN, United Kingdom'}
                autoCapitalize={'none'}
              />
            </View>
          </View>
          <View style={styles.mapScreen}>
            <View style={styles.mapView}>
              <MapView
                provider={PROVIDER_GOOGLE} // remove if not using Google Maps
                style={styles.map}
                region={{
                  latitude: 37.78825,
                  longitude: -122.4324,
                  latitudeDelta: 0.015,
                  longitudeDelta: 0.0121,
                }}></MapView>
            </View>

            <View style={styles.swipeAction}>
              <Image source={constants.images.downArrow} />
              <Text style={styles.swipeTextProps}>
                Swipe up for more information
              </Text>
            </View>

            <View style={styles.therapistInfo}>
              <View
                style={{justifyContent: 'space-around', alignItems: 'center'}}>
                <Image
                  source={constants.images.defaultUserImage}
                  style={{width: 90, height: 90, flex: 1, borderRadius: 10}}
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
                  <Text style={{fontSize: 16}}>DR. MARIE SMITH</Text>
                </View>
                <Text style={{fontSize: 14}}>20 years experience</Text>
                <Text>12 consultations done</Text>
              </View>
            </View>
            <View style={styles.callButton}>
              <CustomButton
                title={'Call Now'}
                submitFunction={() => Alert.alert('clicked')}
              />
            </View>
            <View style={styles.misc}>
              <Text style={styles.textStylesOne}>Search Again</Text>
              <Text style={styles.textStylesTwo}>Cancel</Text>
            </View>
          </View>
        </View>
      </View>
    </GestureRecognizer>
  );
};

export default MapScreen;

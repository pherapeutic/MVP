import React from 'react';
import {StyleSheet, View, Text, Image} from 'react-native';
import {GooglePlacesAutocomplete} from 'react-native-google-places-autocomplete';
import Scale from '../utils/Scale';
import constants from '../utils/constants';

const GooglePlacesInput = (props) => {
  return (
    <View style={{flex: 1}}>
      <GooglePlacesAutocomplete
        // ref={props.onSetLocation}
        value={'hbzbhb'}
        defaultValue={'jkdnjkndcf'}
        placeholder={props.placeholder}
        placeholderTextColor={constants.colors.black}
        onPress={props.onPress}
        textInputProps={{
          textAlign: 'left',
          // marginLeft: Scale.moderateScale(-5),
          paddingLeft: Scale.moderateScale(0),
          textAlignVertical: 'center',
          fontSize: Scale.moderateScale(12),
          width: '95%',
        }}
        // fetchDetails
        renderRightButton={() => (
          <View style={{alignSelf: 'center'}}>
            <Image source={constants.images.ic_location} style={{margin: 10}} />
          </View>
        )}
        styles={styles}
        query={{
          key: 'AIzaSyDjF1VgZXkec7UE6NB5cO61Vt5GvDEIbWk',
          language: 'en',
        }}
      />
    </View>
  );
};

const styles = StyleSheet.create({
  mainContainer: {
    flexDirection: 'row',
    // paddingHorizontal: Scale.moderateScale(5),
    backgroundColor: constants.colors.white,
    height: Scale.moderateScale(25),
    justifyContent: 'space-between',
  },
  container: {
    marginVertical: Scale.moderateScale(5),
    marginHorizontal: Scale.moderateScale(5),
    borderRadius: Scale.moderateScale(5),
  },
  description: {
    fontSize: Scale.moderateScale(12),
    color: constants.colors.black,
  },
  textInputContainer: {
    backgroundColor: constants.colors.white,
    borderTopWidth: 0,
    borderBottomWidth: 0,
    height: 44,
  },
  textInput: {
    backgroundColor: constants.colors.white,
    fontSize: Scale.moderateScale(14),
    paddingHorizontal: Scale.moderateScale(0),
    color: constants.colors.black,
  },
  loader: {},
  listView: {backgroundColor: constants.colors.white},
  predefinedPlacesDescription: {},
  poweredContainer: {
    display: 'none',
  },
  powered: {},
  separator: {},
  row: {},
});

export default GooglePlacesInput;

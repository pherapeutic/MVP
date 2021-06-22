import React, {useCallback} from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  StyleSheet,
  Image,
  Dimensions,
  Platform,
} from 'react-native';
import constants from '../utils/constants';

const Header = (props) => {
  return (
    <View style={styles.backButtonView}>
      {props.LeftComponent ? (
        <View
          style={{
            flex: 0.2,
            justifyContent: 'center',
            alignItems: 'flex-start',
            marginStart: 8,
          }}>
          {props.LeftComponent ? <props.LeftComponent /> : null}
        </View>
      ) : (
        <View style={{flex: 0.2}}>
          <TouchableOpacity
            onPress={() => props.navigation.goBack()}
            style={{
              flex: 1,
              justifyContent: 'center',
              alignItems: 'flex-start',
              marginStart: 8,
            }}>
            <Image
              source={
                props.backicon
                  ? props.backicon
                  : constants.images.backArrowWhite
              }
              style={{height: 18, width: 10, margin: 10}}
            />
          </TouchableOpacity>
        </View>
      )}

      <View
        style={{
          flex: props.titleflex ? props.titleflex : 1,
          justifyContent: 'center',
          alignItems: 'center',
        }}>
        <Text
          style={[
            styles.headingText,
            {
              color: props.titleColor
                ? props.titleColor
                : constants.colors.white,
            },
          ]}>
          {props.title}
        </Text>
      </View>

      <View
        style={{
          flex: 0.2,
          justifyContent: 'center',
          alignItems: 'flex-end',
          marginEnd: 8,
        }}>
        {props.RightComponent ? <props.RightComponent /> : null}
      </View>
    </View>
  );
};

export default Header;
const styles = StyleSheet.create({
  container: {
    flexDirection: 'row',
    width: Dimensions.get('window').width * 1,
    // alignItems: "center",
    // justifyContent: "space-between",
    // paddingHorizontal: Scale.moderateScale(10),
    marginVertical: 10,
  },
  backButtonView: {
    flex: 1,
    paddingTop: 30,
    width: Dimensions.get('window').width,
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
  },
  title: {
    // flex: 1,
    fontSize: 18,
    // fontWeight: 'bold',
    textAlign: 'center',
    padding: 5,
    color: constants.colors.white,
  },
  headingText: {
    fontSize: 20,
    color: constants.colors.white,
    fontWeight: '500',
  },
});

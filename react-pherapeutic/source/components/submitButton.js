import React from 'react';
import {
  Text,
  TouchableOpacity,
  Dimensions,
  StyleSheet
} from 'react-native';
import constants from '../utils/constants';
import LinearGradient from 'react-native-linear-gradient';

const SubmitButton = (props) => {
  const {
    title,
    submitFunction,
    size,
    empty
  } = props;

  function setWidth(size) {
    switch (size) {
      case 'Default':
        return Dimensions.get('window').width * 0.6
      case 'Medium':
        return Dimensions.get('window').width * 0.4
      case 'Small':
        return Dimensions.get('window').width * 0.3
    }
  }

  return (
    <TouchableOpacity
      style={{ justifyContent: 'center', alignItems: 'center' }}
      onPress={() => submitFunction()}
    >
      <LinearGradient
        start={{ x: 0, y: 1 }}
        end={{ x: 1, y: 1 }}
        colors={empty ? ['transparent', 'transparent'] : ["#228994", "#3BD8E5"]}
        style={{
          justifyContent: 'center',
          alignItems: 'center',
          height: Dimensions.get('window').height * 0.052,
          width: setWidth(size || 'Default'),
          borderRadius: 3,
          borderColor: constants.colors.darkGreen,
          borderWidth: empty ? 1 : 0
          // marginVertical: Dimensions.get('window').height * 0.005
        }}
      >
        <Text
          style={{
            color: empty ? constants.colors.darkGreen : constants.colors.white,
            fontWeight: '500',
            fontSize: 16
          }}
        >{title}</Text>
      </LinearGradient>
    </TouchableOpacity>
  )
};

export default SubmitButton;
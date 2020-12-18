import React, { useState } from "react";
import {
  View,
  Text,
  Modal,
  Image,
  Linking,
  TextInput,
  Dimensions,
  StyleSheet,
  TouchableOpacity,
} from "react-native";
import constants from '../utils/constants';
import AsyncStorage from '@react-native-async-storage/async-storage';
import Events from '../utils/events';

const H = Dimensions.get("window").height;
const W = Dimensions.get("window").width;

const styles = StyleSheet.create({
  modalConatainer: {
    flex: 1,
    opacity: 0.5,
    backgroundColor: "black",
  },
  cover: {
    flex: 1,
    flexDirection: "column",
    position: "absolute",
    top: 0,
    bottom: 0,
    left: 0,
    right: 0,
    justifyContent: "center",
    alignItems: "center"
  },
  contentWrapper: {
    height: H * 0.25,
    width: W * 0.75,
    borderRadius: 10,
    backgroundColor: constants.colors.white,
    justifyContent: 'center',
    alignItems: 'center'
  },
  upperView: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center'
  },
  lowerView: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    flexDirection: 'row'
  },
  buttons: {
    height: H * 0.04,
    width: W * 0.2,
    justifyContent: 'center',
    alignItems: 'center',
    borderRadius: 2,
    borderWidth: 0
  },
  buttonText: {
    color: constants.colors.black,
    fontSize: 13,
    fontWeight: '500'
  },
  logoutText: {
    color: constants.colors.black,
    fontSize: 18,
    fontWeight: '600',
    lineHeight: 25
  }
});

const LogoutAlert = (props) => {
  const [visible, setVisible] = useState(true);

  const { showModal, setShowModal, navigation } = props;

  return (
    <Modal
      visible={showModal}
      transparent={true}
      animationType='fade'
    >
      <View style={styles.modalConatainer} />
      <View style={styles.cover} >
        <View style={styles.contentWrapper} >
          <View style={styles.upperView} >
            <Text style={styles.logoutText} >Are you sure, you want</Text>
            <Text style={styles.logoutText} >to logout?</Text>
          </View>
          <View style={styles.lowerView} >
            <TouchableOpacity
              onPress={() => {
                AsyncStorage.removeItem("userData")
                setShowModal(false)
                navigation.navigate('auth', { screen: 'Login' });
              }}
              style={[styles.buttons, { backgroundColor: '#25D0E1', marginRight: 25 }]}
            >
              <Text style={styles.buttonText} >Sure</Text>
            </TouchableOpacity>
            <TouchableOpacity
              style={[styles.buttons, { backgroundColor: '#ededed', marginLeft: 25 }]}
              onPress={() => setShowModal(false)}
            >
              <Text style={styles.buttonText} >Cancel</Text>
            </TouchableOpacity>
          </View>
        </View>
      </View>
    </Modal>
  )
};

export default LogoutAlert;
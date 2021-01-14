import 'react-native-gesture-handler';
import { enableScreens } from 'react-native-screens';
enableScreens();
import React, { useEffect, useState } from 'react';
import { StyleSheet, SafeAreaView, Dimensions, Text, View, Alert, StatusBar,Image} from 'react-native';
import AppNavigator from './source/navigator';
import ModalLoader from './source/components/modalLoader';
import Events from './source/utils/events';
import { Provider } from "react-redux";
import store from "./source/redux/index";
import constants from './source/utils/constants';
import { utils } from '@react-native-firebase/app';
import messaging, { firebase } from '@react-native-firebase/messaging';
import AsyncStorage from '@react-native-async-storage/async-storage';
import SplashScreen from 'react-native-splash-screen'
import Modal from 'react-native-modal';
import NetInfo from "@react-native-community/netinfo";
const { height, width } = Dimensions.get('window');

export default function App() {
  const [showModal, setShowModal] = useState(false);
  const [loaderData, setLoaderData] = useState({ label: "", backgroundColor: "black" })
  const [hasPermission, setPermission] = useState(false);
  const [isConnected, setConnected] = useState('');
  const [isModalVisible, setModalVisible] = useState(false);

  requestUserPermission = async () => {
    const authStatus = await messaging().requestPermission();
    const enabled =
      authStatus === messaging.AuthorizationStatus.AUTHORIZED ||
      authStatus === messaging.AuthorizationStatus.PROVISIONAL;

    if (enabled) {
      console.log('Authorization status:', authStatus);
      setPermission(true)
    }
  }

  getFcmToken = async () => {
    console.log("Getting fcm token!!!");
    const fcmToken = await messaging().getToken();
    if (fcmToken) {
      console.log("Your Firebase Token is => ", fcmToken);
      AsyncStorage.setItem('fcmToken', fcmToken);
    } else {
      console.log("Failed", "No token received");
    }
  }

  useEffect(() => {
    SplashScreen.hide();
    if (hasPermission) {
      getFcmToken()
    } else {
      requestUserPermission()
    }
    // const unsubscribe = messaging().onMessage(async remoteMessage => {
    // //  console.log(JSON.stringify(remoteMessage))
    //  // console.log(remoteMessage.data.title)
    //  // Alert.alert('A new FCM message arrived!');
   
    // });
 

   

   // return unsubscribe;
  }, [hasPermission])
  useEffect(() => {
    const unsubscribe = NetInfo.addEventListener(state => {
      console.log("Connection type", state.type);
      console.log("Is connected?1", state.isConnected);
      if (state.isConnected == true) {
        setModalVisible(false)
        setConnected(state.isConnected)
      }
      else {
        setConnected(state.isConnected)
        setModalVisible(true)
      }
  
    });
      }, [])

  Events.on("showModalLoader", "sml", (data) => {
    setShowModal(true);
    setLoaderData(data);
    console.log("triggered!!!")
  });

  Events.on("hideModalLoader", "hml", () => {
    setShowModal(false)
  });
  return (
    <Provider store={store} style={{ flex: 1 }}>
      <View style={styles.safeAreaView}>
        <StatusBar barStyle='light-content' hidden={false} translucent={false} />
         
  { isConnected == false ?
   // return (
      <View
        style={{
          flex: 1,
          justifyContent: 'center',
          alignItems: 'center',
          height: Dimensions.get('window').height,
          width: Dimensions.get('window').width,
          backgroundColor: 'transparent',
        }}>
        <Modal isVisible={isModalVisible}>
          <View style={{ backgroundColor: 'white', borderRadius: 5, padding: 10 }}>
            <Image style={{ alignSelf: 'center' }} source={constants.images.Internerconnection} />
       


          </View>
        </Modal>
      </View>
    //);
    :

    <AppNavigator />
  }
 
      
        
        <ModalLoader
          data={loaderData}
          show={showModal}
        />
      </View>
    </Provider>
  );
}

const styles = StyleSheet.create({
  safeAreaView: {
    flex: 1,
    backgroundColor: '#ffffff'
  },
  containerBackground: {
    height: '100%',
    width,
    position: 'absolute',
    top: 0,
    left: 0,
    bottom: 0,
    right: 0,
    zIndex: -2
  },
  container: {
    flex: 1,
    backgroundColor: '#ffffff',
    alignItems: 'center',
    justifyContent: 'center',
  }
});
import React, { useState, useEffect } from 'react';
import { connect } from 'react-redux';
import { PermissionsAndroid, Alert } from 'react-native'
import QuestionBot from '../questionBot';
import TherapistStatus from '../therapistStatus';

import messaging, { firebase } from '@react-native-firebase/messaging';
const requestCameraAndAudioPermission = async () => {
  try {
    const granted = await PermissionsAndroid.requestMultiple([
      PermissionsAndroid.PERMISSIONS.CAMERA,
      PermissionsAndroid.PERMISSIONS.RECORD_AUDIO,
    ])
    if (
      granted['android.permission.RECORD_AUDIO'] === PermissionsAndroid.RESULTS.GRANTED
      && granted['android.permission.CAMERA'] === PermissionsAndroid.RESULTS.GRANTED
    ) {
      console.log('You can use the cameras & mic')
    } else {
      console.log('Permission denied')
    }
  } catch (err) {
    console.warn(err)
  }
}
const Home = (props) => {
  const { userData, navigation } = props;
  useEffect(() => {
    const unsubscribe = messaging().onMessage(async remoteMessage => {
      console.log('onmessage')
      console.log('notificaation' + JSON.stringify(remoteMessage))
      console.log('sound' + JSON.stringify(remoteMessage))
      //  Alert.alert('A new FCM message arrived!', JSON.stringify(remoteMessage));
      Alert.alert(
        '',
        '' + remoteMessage.notification.title + '',
        [
          {
            text: "Cancel",
            onPress: () => console.log("Cancel Pressed"),
            style: "cancel"
          },
          {
            text: "Join",
            onPress: () => navigation.navigate('VideoCall', {
              channelnamedata: remoteMessage.data.channel_name,
              caller_id_remotedata:remoteMessage.data.caller_id
            })
          }
        ],
        { cancelable: false }
      );
    });
    return unsubscribe;
  }, [])
  useEffect(() => {
    // Assume a message-notification contains a "type" property in the data payload of the screen to open

    messaging().onNotificationOpenedApp(remoteMessage => {
      console.log(
        'Notification caused app to open from background state:',
        remoteMessage,
      );
      //setInitialRoute('VideoCall')
      navigation.navigate('VideoCall', {
        channelnamedata: remoteMessage.data.channel_name,
        caller_id_remotedata:remoteMessage.data.caller_id
      });

    });

    // Check whether an initial notification is available
    messaging()
      .getInitialNotification()
      .then(remoteMessage => {
        if (remoteMessage) {
          console.log(
            'Notification caused app to open from quit state:',
            remoteMessage,
          );
          // setInitialRoute('VideoCall') // e.g. "Settings"
          navigation.navigate('VideoCall', {
            channelnamedata: remoteMessage.data.channel_name,
            caller_id_remotedata:remoteMessage.data.caller_id
          });

        }

      });
  }, []);

  if (Platform.OS === 'android') {
    requestCameraAndAudioPermission().then(() => {
      console.log('requested!')
    })
  }
  if (userData.role == 1)
    return <TherapistStatus navigation={navigation} />
  else
    return <QuestionBot navigation={navigation} />
}

const mapStateToProps = (state) => ({
  userData: state.user.userData,
  userToken: state.user.userToken
});

export default connect(mapStateToProps)(Home);
// import React, { useState } from 'react';
// import AgoraUIKit from 'agora-rn-uikit';

// const App = () => {
//   const [videoCall, setVideoCall] = useState(true);
//   const rtcProps = {
//     appId: '0d97119f3b6744d58af674a7abdd76d1',
//     channel: 'test',
//     peerIds:[]
//     // const [peerIds, setPeerIds] = useState([]); //Array for storing connected peers
// //   const [appid, setAppid] = useState('0d97119f3b6744d58af674a7abdd76d1');
// //   const [uid, setUID] = useState(null); //Generate a UID for local user
// //   const [channelName, setChannelName] = useState("videoCallChannel"); //Channel Name for the current session
// //   const [vidMute, setVidMute] = useState(false); //State variable for Video Mute
// //   const [audMute, setAudMute] = useState(false);  //State variable for Audio Mute
// //   const [joinSucceeed, setJoinSucceed] = useState(true); //State variable for storing success;
//   };
//   const callbacks = {
//     EndCall: () => setVideoCall(false),
//   };
//   return videoCall ? (
//     <AgoraUIKit rtcProps={rtcProps} callbacks={callbacks} />
//   ) : (
//     <></>
//   );
// };

// export default App;

import React, { useEffect, useState } from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  NativeModules,
  Platform,
  Dimensions,
  PermissionsAndroid
} from 'react-native';
import styles from './styles';
import AgoraView, {  RtcEngine } from 'react-native-agora';
import { connect } from 'react-redux';
// RtcEngine.create('0d97119f3b6744d58af674a7abdd76d1');
const { height, width } = Dimensions.get('window')

const { Agora } = NativeModules;

// const { FPS30, AudioProfileDefault, AudioScenarioDefault, Adaptative } = Agora;

const config = {
  appid: '0d97119f3b6744d58af674a7abdd76d1',
  channelProfile: 0,
  mode: 1,
  videoEncoderConfig: {
    width: width,
    height: height,
    bitrate: 1,
    // frameRate: FPS30,
    // orientationMode: Adaptative,
  },
  // audioProfile: AudioProfileDefault,
  // audioScenario: AudioScenarioDefault
}

const VideoCall = (props) => {
  // const [peerIds, setPeerIds] = useState([32, 48]); //Array for storing connected peers
  const [peerIds, setPeerIds] = useState([]); //Array for storing connected peers
  const [appid, setAppid] = useState('0d97119f3b6744d58af674a7abdd76d1');
  const [uid, setUID] = useState(null); //Generate a UID for local user
  const [channelName, setChannelName] = useState("videoCallChannel"); //Channel Name for the current session
  const [vidMute, setVidMute] = useState(false); //State variable for Video Mute
  const [audMute, setAudMute] = useState(false);  //State variable for Audio Mute
  const [joinSucceeed, setJoinSucceed] = useState(true); //State variable for storing success;
  // RtcEngine.init(config);

  const { userData } = props;

  const askPermissions = async () => {
    if (Platform.OS === 'android') {
      await PermissionsAndroid.requestMultiple([
        PermissionsAndroid.PERMISSIONS.RECORD_AUDIO,
        PermissionsAndroid.PERMISSIONS.CAMERA,
      ]);
    }
  }

  useEffect(() => {
    setUID(userData.user_id);
    askPermissions()
    // RtcEngine.on(‘userJoined’, (data) => {
    //   //Get currrent peer IDs
    //   if (peerIds.indexOf(data.uid) === -1) { //If new user has joined
    //     setPeerIds(prevIDs => [...prevIDs, data.uid]); //add peer ID to state array
    //   }
    // });
    // RtcEngine.on(‘userOffline’, (data) => { //If user leaves
    //   setPeerIds(prevIDs => prevIDs.filter(uid => uid !== data.uid)) //remove peer ID from state array
    // });
    // RtcEngine.on(‘joinChannelSuccess’, (data) => { //If Local user joins RTC channel
    //   RtcEngine.startPreview(); //Start RTC preview
    //   setJoinSucceed(true) //Set state variable to true
    // });
    RtcEngine.joinChannel(channelName, uid); //Join Channel
    RtcEngine.enableAudio(); //Enable the audio
  }, []);

  const toggleAudio = () => {
    RtcEngine.muteLocalAudioStream(!audMute);
    setAudMute(prevValue => !prevValue)
  }
  const toggleVideo = () => {
    setVidmut(prevValue => !prevValue);
    RtcEngine.muteLocalVideoStream(!vidMute);
  }
  const endCall = () => {
    RtcEngine.destroy();
    Actions.home();
  }

  const VideoView = () => {
  }
  
  if (peerIds.length == 2)
    return (
      <View style={{ flex: 1 }}>
        <AgoraView style={{ flex: 1 }}
          remoteUid={peerIds[0]}
          mode={1} />

        <AgoraView style={{ flex: 1 }}
          remoteUid={peerIds[1]}
          mode={1} />
      </View>
    )
  else if (peerIds.length == 1)
    return (
      <AgoraView style={{ flex: 1 }}
        remoteUid={peerIds[0]}
        mode={1} />
    )
  else
    return (
      <AgoraView style={{ flex: 1 }}
        // remoteUid={peerIds[0]}
        zOrderMediaOverlay={true}
        showLocalVideo={true}
        mode={1} />
    )
  return (
    <View style={styles.container} >
      <VideoView />
    </View>
  )
};

const mapStateToProps = (state) => ({
  userdata: state.user.userdata
})

export default connect(mapStateToProps)(VideoCall);
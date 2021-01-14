import React, {Component} from 'react';
import {
  FlatList,
  ImageBackground,
  Dimensions,
  Image,
  Platform,
  ScrollView,
  Text,
  TouchableOpacity,
  View,
  PermissionsAndroid,
  Button,
} from 'react-native';
import RtcEngine, {
  RtcLocalView,
  RtcRemoteView,
  VideoRenderMode,
} from 'react-native-agora';
import styles from './styles';
import constants from '../../utils/constants';
import APICaller from '../../utils/APICaller';
import {connect} from 'react-redux';
import AsyncStorage from '@react-native-async-storage/async-storage';
import Events from '../../utils/events';

const {height, width} = Dimensions.get('window');
const requestCameraAndAudioPermission = async () => {
  try {
    const granted = await PermissionsAndroid.requestMultiple([
      PermissionsAndroid.PERMISSIONS.CAMERA,
      PermissionsAndroid.PERMISSIONS.RECORD_AUDIO,
    ]);
    if (
      granted['android.permission.RECORD_AUDIO'] ===
        PermissionsAndroid.RESULTS.GRANTED &&
      granted['android.permission.CAMERA'] ===
        PermissionsAndroid.RESULTS.GRANTED
    ) {
      console.log('You can use the cameras & mic');
    } else {
      console.log('Permission denied');
    }
  } catch (err) {
    console.warn(err);
  }
};

interface Props {}

interface State {
  appId: string;
  channelName: string;
  token: string;
  joinSucceed: boolean;
  peerIds: number[];
}

class App extends Component<Props, State> {
  _engine: RtcEngine;

  constructor(props) {
    super(props);
    const {channelnamedata, callDetails} = this.props.route.params;
    this.state = {
      appId: 'd54602ec57f14ee38079d5b2f0cd7438',
      channelName: channelnamedata,
      callDetails: callDetails,
      userData: null,
      token: '',
      joinSucceed: false,
      peerIds: [],
      uid: 0,
      openMicrophone: true,
      enableSpeakerphone: true,
      Mute: false,
      CallReciverName: '',
      user_joined: false,
      timer: 0,
      timercalloff: 0,
    };
    this.init();
    if (Platform.OS === 'android') {
      requestCameraAndAudioPermission().then(() => {
        console.log('requested!');
      });
    }
  }

  UNSAFE_componentWillMount() {
    if (this.props.route.params) {
      const {CallReciverName} = this.props.route.params;
      if (CallReciverName) {
        this.setState({
          CallReciverName: CallReciverName,
        });
      }
    }
    this.getTherapistsList();
    setTimeout(() => {
      this.startCall();
    }, 1000);

    const {userToken, navigation, userData} = this.props;
    this.setState({userData: userData});
  }
  getTherapistsList = () => {
    const {userToken, navigation, userData} = this.props;

    // alert(JSON.stringify(userData));
    const endpoint = 'agoraToken';
    const method = 'POST';
    const headers = {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${userToken}`,
      Accept: 'application/json',
    };
    let body = new FormData();
    body.append('channel_name', this.state.channelName);
    body.append('uid', this.state.uid);
    APICaller(endpoint, method, body, headers)
      .then((response) => {
        // console.log(
        //   'response getting therapist list => ',
        //   response['data'],
        //   '------',
        // );
        const {status, statusCode, message, data} = response['data'];
        this.setState({
          token: data.token,
        });
      })
      .catch((error) => {
        //  alert(error);
        console.log('error response getting agora token => ', error['data']);
      });
  };

  // Turn the microphone on or off.

  switchMicrophone = () => {
    const {openMicrophone} = this.state;
    this._engine
      ?.enableLocalAudio(!openMicrophone)
      .then(() => {
        this.setState({openMicrophone: !openMicrophone});
      })
      .catch((err) => {
        console.warn('enableLocalAudio', err);
      });
  };

  updateCallLogs = (duration, call_status, ended_at) => {
    const {userToken, navigation, userData} = this.props;
    const {callDetails} = this.state;
    if (call_status === 2) {
      Events.trigger('showModalLoader');
    }

    const endpoint = 'updateCallLog';
    const method = 'POST';
    const headers = {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${userToken}`,
      Accept: 'application/json',
    };
    var body = {
      caller_id: callDetails.caller_id,
      duration: duration,
      call_status: call_status,
      ended_at: ended_at,
    };
    console.log('data before sending call logs=> ', body);
    // alert(JSON.stringify(body));
    APICaller(endpoint, method, body, headers)
      .then((response) => {
        Events.trigger('hideModalLoader');

        console.log('response after updating call logs => ', response['data']);
        const {status, statusCode, message, data} = response['data'];
        if (call_status === 2) {
          this.leaveAndCleanUpResources();
        }
      })
      .catch((error) => {
        Events.trigger('hideModalLoader');
        console.log('error updating call logs => ', error['data']);
        if (call_status === 2) {
          this.leaveAndCleanUpResources();
        }
      });
  };

  // Switch the audio playback device.
  switchSpeakerphone = () => {
    const {enableSpeakerphone} = this.state;
    this._engine
      ?.setEnableSpeakerphone(!enableSpeakerphone)
      .then(() => {
        this.setState({enableSpeakerphone: !enableSpeakerphone});
      })
      .catch((err) => {
        console.warn('setEnableSpeakerphone', err);
      });
  };

  switchMuteAudio = () => {
    console.log('Mute');
    const {Mute} = this.state;
    this._engine.enableAudio();
    this.setState({Mute: false});
  };
  switchUnMuteAudio = () => {
    console.log('unMute');
    const {Mute} = this.state;
    this._engine.disableAudio();
    this.setState({Mute: true});
  };
  init = async () => {
    const {appId} = this.state;
    this._engine = await RtcEngine.create(appId);
    this._engine.enableVideo();
    this._engine.enableAudio();
    //await this._engine.disableAudio()

    this._engine.addListener('UserJoined', (uid, elapsed) => {
      console.log('UserJoined', uid, elapsed);
      this.setState({
        user_joined: true,
      });
      const {peerIds} = this.state;
      if (peerIds.indexOf(uid) === -1) {
        this.setState({
          peerIds: [...peerIds, uid],
        });
      }
    });

    this._engine.addListener('UserOffline', (uid, reason) => {
      console.log('UserOffline', uid, reason);
      const {peerIds} = this.state;
      this.setState({
        peerIds: peerIds.filter((id) => id !== uid),
        user_joined: false,
        timer: 0,
      });
      this.endCall();
    });

    this._engine.addListener('JoinChannelSuccess', (channel, uid, elapsed) => {
      console.log('JoinChannelSuccess', channel, uid, elapsed);
      this.setState({
        joinSucceed: true,
        uid: uid,
      });
    });
  };
  startCall = async () => {
    this.interval = setInterval(() => {
      var timer = this.state.timer;
      timer = timer + 1;
      this.setState((prevState) => ({timer: timer}));

      // if role is client
      if (this.state.userData && this.state.userData.role === '0') {
        // update Call Logs After every 2 seconds
        if (timer % 2 === 0) {
          this.updateCallLogs(timer, 1, null);
        }
      }
    }, 1000);
    this.intervalend = setInterval(() => {
      if (this.state.peerIds.length <= 0) {
        this.endCall();
      }
    }, 60000);

    this.setState({
      joinSucceed: true,
    });
    console.log(
      'start call called !!!',
      this.state.token,
      this.state.channelName,
    );
    this._engine.joinChannel(this.state.token, this.state.channelName, null, 0);
  };

  _renderVideos = () => {
    const {joinSucceed} = this.state;
    return joinSucceed ? (
      <View style={styles.fullView}>
        <RtcLocalView.SurfaceView
          style={styles.max}
          channelId={this.state.channelName}
          // renderMode={VideoRenderMode.Hidden}
        />
        {this._renderRemoteVideos()}
      </View>
    ) : null;
  };

  _renderRemoteVideos = () => {
    const {peerIds} = this.state;

    return (
      <ScrollView
        style={styles.remoteContainer}
        contentContainerStyle={{paddingHorizontal: 2.5}}
        horizontal={true}>
        {peerIds.map((value, index, array) => {
          return (
            <RtcRemoteView.SurfaceView
              style={styles.remote}
              uid={value}
              channelId={this.state.channelName}
              renderMode={VideoRenderMode.Hidden}
              zOrderMediaOverlay={true}
            />
          );
        })}
      </ScrollView>
    );
  };

  endCall = async () => {
    // if(this.state.timer === 1){

    // if role is client
    if (this.state.userData && this.state.userData.role === 0) {
      // update Call Logs After Call End
      this.updateCallLogs(this.state.timer, 2, 1);
    } else {
      this.leaveAndCleanUpResources();
    }
  };

  leaveAndCleanUpResources = async () => {
    clearInterval(this.interval);
    clearInterval(this.intervalend);
    // }
    await this._engine.leaveChannel();
    this.setState({peerIds: [], joinSucceed: false});
   if(this.state.userData.role==0)
   {
    this.props.navigation.navigate('TherapistDetails');
   }
   else{
    this.props.navigation.navigate('Home'); 
   }
   
      
   // this.props.navigation.goBack()
  };
  secondsToHms(d) {
    d = Number(d);
    var h = Math.floor(d / 3600);
    var m = Math.floor((d % 3600) / 60);
    var s = Math.floor((d % 3600) % 60);

    // var hDisplay = h > 0 ? h + (h == 1 ? " hour, " : "0:") : "";
    // var mDisplay = m > 0 ? m + (m == 1 ? " minute, " : "0:") : "";
    // var sDisplay = s > 0 ? s + (s == 1 ? " second" : ":") : "";
    // return hDisplay + mDisplay + sDisplay;
    var hDisplay = h > 0 ? h + (h == 1 ? '' : '') : '';
    var mDisplay = m > 0 ? m + (m == 1 ? '' : '') : '';
    var sDisplay = s > 0 ? s + (s == 1 ? '' : '') : '';
    if (hDisplay != '') {
      return (
        (hDisplay.length > 1 ? hDisplay : '0' + hDisplay) +
        ':' +
        (mDisplay.length > 1 ? mDisplay : '0' + mDisplay) +
        ':' +
        (sDisplay.length > 1 ? sDisplay : '0' + sDisplay)
      );
    } else if (mDisplay != '') {
      return (
        (mDisplay.length > 1 ? mDisplay : '0' + mDisplay) +
        ':' +
        (sDisplay.length > 1 ? sDisplay : '0' + sDisplay)
      );
    } else if (sDisplay != '') {
      return '00:' + (sDisplay.length > 1 ? sDisplay : '0' + sDisplay);
    }
    return '00:00';
  }
  render() {
    return (
      <View style={styles.max}>
        <View style={styles.max}>
          {this._renderVideos()}
          {this.state.user_joined == false && this.state.CallReciverName != '' && (
            <ImageBackground
              source={constants.images.calling_transparent_bg}
              style={{
                padding: 5,
                marginTop: 10,
                borderRadius: 10,
                position: 'absolute',
                zIndex: 0,
              }}>
              <Text
                style={{
                  color: 'white',
                  paddingTop: 7,
                  paddingBottom: 7,
                  fontSize: 15,
                }}>
                {this.state.CallReciverName}
              </Text>
              <Text style={{color: 'white', fontSize: 15}}>Calling......</Text>
            </ImageBackground>
          )}
          {this.state.user_joined == true && (
            <ImageBackground
              source={constants.images.time_transparent_bg}
              style={{
                position: 'absolute',
                top: height - 210,
                zIndex: 0,
                flexDirection: 'row',
                alignItems: 'center',
                alignSelf: 'center',
              }}>
              <Text style={{color: 'white', padding: 15}}>
                {' '}
                {this.secondsToHms(this.state.timer)}
              </Text>
            </ImageBackground>
          )}
          <View
            style={{
              width: width,
              alignSelf: 'center',
              position: 'absolute',
              top: height - 150,
              zIndex: 0,
              flexDirection: 'row',
              justifyContent: 'space-around',
              alignItems: 'center',
            }}>
            <TouchableOpacity
              onPress={
                this.state.Mute ? this.switchMuteAudio : this.switchUnMuteAudio
              }
              style={{backgroundColor: 'white', borderRadius: 100}}>
              <Image
                source={
                  this.state.Mute
                    ? constants.images.no_sound
                    : constants.images.mic_icon
                }
                style={{margin: 15}}
              />
            </TouchableOpacity>
            <TouchableOpacity
              onPress={this.startCall}
              style={{
                backgroundColor: 'white',
                borderRadius: 100,
              }}>
              <Image
                source={constants.images.reconnect_icon}
                style={{margin: 15}}
              />
            </TouchableOpacity>
            <TouchableOpacity
              onPress={this.endCall}
              style={{
                backgroundColor: 'red',
                borderRadius: 100,
              }}>
              <Image
                source={constants.images.disconnect_icon}
                style={{margin: 15}}
              />
            </TouchableOpacity>
          </View>
        </View>
      </View>
    );
  }
}
const mapStateToProps = (state) => ({
  userToken: state.user.userToken,
  userData: state.user.userData,
});

export default connect(mapStateToProps)(App);

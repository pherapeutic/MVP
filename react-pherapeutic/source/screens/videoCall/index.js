import React, { Component } from 'react'
import { Platform, ScrollView, Text, TouchableOpacity, View, PermissionsAndroid } from 'react-native'
import RtcEngine, { RtcLocalView, RtcRemoteView, VideoRenderMode } from 'react-native-agora';
import styles from './styles';

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


interface Props {
}


interface State {
  appId: string,
  channelName: string,
  token: string,
  joinSucceed: boolean,
  peerIds: number[],
}


export default class App extends Component<Props, State> {
  _engine: RtcEngine
  
  constructor(props) {
    super(props)
    this.state = {
      appId: '0d97119f3b6744d58af674a7abdd76d1',
      channelName: 'yourChannel',
      token: 'yourToken',
      joinSucceed: true,
      peerIds: [],
    }
    if (Platform.OS === 'android') {
      requestCameraAndAudioPermission().then(() => {
        console.log('requested!')
      })
    }
  }

  componentDidMount() {
    this.init()
  }

  init = async () => {
    const { appId } = this.state
    this._engine = await RtcEngine.create(appId)
    await this._engine.enableVideo()

    this._engine.addListener('UserJoined', (uid, elapsed) => {
      console.log('UserJoined', uid, elapsed)
      const { peerIds } = this.state
      if (peerIds.indexOf(uid) === -1) {
        this.setState({
          peerIds: [...peerIds, uid]
        })
      }
    })

    this._engine.addListener('UserOffline', (uid, reason) => {
      console.log('UserOffline', uid, reason)
      const { peerIds } = this.state
      this.setState({
        
        peerIds: peerIds.filter(id => id !== uid)
      })
    })

    
    this._engine.addListener('JoinChannelSuccess', (channel, uid, elapsed) => {
      console.log('JoinChannelSuccess', channel, uid, elapsed)
      this.setState({
        joinSucceed: true
      })
    })
  }

  
  startCall = async () => {
    console.log("start call called !!!")
    await this._engine.joinChannel(this.state.token, this.state.channelName, null, 0)
  }

  _renderVideos = () => {
    const { joinSucceed } = this.state
    return joinSucceed 
    ? (
      <View style={styles.fullView}>
        <RtcLocalView.SurfaceView
          style={styles.max}
          channelId={this.state.channelName}
          renderMode={VideoRenderMode.Hidden} />
        {this._renderRemoteVideos()}
      </View>
    ) : null
  }

  _renderRemoteVideos = () => {
    const { peerIds } = this.state
    return (
      <ScrollView
        style={styles.remoteContainer}
        contentContainerStyle={{ paddingHorizontal: 2.5 }}
        horizontal={true}>
        {peerIds.map((value, index, array) => {
          return (
            <RtcRemoteView.SurfaceView
              style={styles.remote}
              uid={value}
              channelId={this.state.channelName}
              renderMode={VideoRenderMode.Hidden}
              zOrderMediaOverlay={true} />
          )
        })}
      </ScrollView>
    )
  }

  endCall = async () => {
    await this._engine.leaveChannel()
    this.setState({ peerIds: [], joinSucceed: false })
  }
  render() {

    return (
      <View style={styles.max}>
        <View style={styles.max}>
          <View style={styles.buttonHolder}>
            <TouchableOpacity
              onPress={this.startCall}
              style={styles.button}>
              <Text style={styles.buttonText}> Start Call </Text>
            </TouchableOpacity>
            <TouchableOpacity
              onPress={this.endCall}
              style={styles.button}>
              <Text style={styles.buttonText}> End Call </Text>
            </TouchableOpacity>
          </View>
          {this._renderVideos()}
        </View>
      </View>
    )
  }

}


/*
import React, { useState } from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  Dimensions
} from 'react-native';
import { connect } from 'react-redux'

const VideoCall = (props) => {
  const [videoCall, setVideoCall] = useState(true);
  const rtcProps = {
    appId: '0d97119f3b6744d58af674a7abdd76d1',
    channel: 'test',
    peerIds: []
    // const [peerIds, setPeerIds] = useState([]); //Array for storing connected peers
    //   const [appid, setAppid] = useState('0d97119f3b6744d58af674a7abdd76d1');
    //   const [uid, setUID] = useState(null); //Generate a UID for local user
    //   const [channelName, setChannelName] = useState("videoCallChannel"); //Channel Name for the current session
    //   const [vidMute, setVidMute] = useState(false); //State variable for Video Mute
    //   const [audMute, setAudMute] = useState(false);  //State variable for Audio Mute
    //   const [joinSucceeed, setJoinSucceed] = useState(true); //State variable for storing success;
  };
  const callbacks = {
    EndCall: () => setVideoCall(false),
  };
  return (
    <View style={styles.container} >
      <Text>Video Call</Text>
    </View>
  )
};

const mapStateToProps = (state) => ({
  userdata: state.user.userdata
})

export default connect(mapStateToProps)(VideoCall);

*/
import React, { useState, } from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  StyleSheet,
  Dimensions,
  Image,
  TextInput
} from 'react-native';
import constants from '../../utils/constants';
import styles from './styles';
import SubmitButton from '../../components/submitButton';
import { connect } from 'react-redux';
import Video from 'react-native-video'
import LinearGradient from 'react-native-linear-gradient';

const { height, width } = Dimensions.get('window');

const GetStarted = (props) => {
  const [paused, setpaused] = useState(true);

  const { navigation, dispatch } = props;

  return (
    <View style={styles.container} >
      <Video
        source={{ uri: 'https://firebasestorage.googleapis.com/v0/b/ihad2lie.appspot.com/o/posts%2Fnull?alt=media&token=fd64bba7-f73e-44d2-b166-eebe38bf3cfb' }}
        style={styles.containerBackground}
        paused={paused}
        fullscreen={false}
        resizeMode={'stretch'}
      />

      <View style={styles.backButtonView} >
        <TouchableOpacity
          onPress={() => {
            setpaused(true);
            navigation.navigate('AuthOptions')
          }}
          style={{ justifyContent: 'center', alignItems: 'center' }}
        >
          <LinearGradient
            start={{ x: 0, y: 1 }}
            end={{ x: 1, y: 1 }}
            colors={["#228994", "#3BD8E5"]}
            style={{
              justifyContent: 'center',
              alignItems: 'center',
              height: Dimensions.get('window').height * 0.032,
              width: Dimensions.get('window').width * 0.15,
              borderRadius: 3,
            }}
          >
            <Text
              style={{
                color: constants.colors.white,
                fontWeight: '500',
                fontSize: 13
              }}
            >Skip</Text>
          </LinearGradient>
        </TouchableOpacity>
      </View>
      <TouchableOpacity
        onPress={() => setpaused(prevState => !prevState)}
        style={styles.middleView}
      >
        {
          paused
            ?
            <TouchableOpacity
              onPress={() => setpaused(prevState => !prevState)}
              style={{ justifyContent: 'center', alignItems: 'center' }}
            >
              <Image source={constants.images.playPause} style={{}} />
            </TouchableOpacity>
            :
            <TouchableOpacity onPress={() => setpaused(prevState => !prevState) } style={{flex: 1, width}} />
        }
      </TouchableOpacity>
      <View style={styles.footerView} >
        <View style={styles.footerSubview} >
          <SubmitButton
            title={'Get Started'}
            submitFunction={() => {
              setpaused(true);
              navigation.navigate('AuthOptions')
            }}
          />
        </View>
        <View style={styles.footerSubview} >
          <Text style={styles.footerText} >Already signed up? <Text
            onPress={() => {
              setpaused(true);
              navigation.navigate('Login')
            }}
            style={styles.footerlinkText}
          >Login</Text></Text>
        </View>
      </View>
    </View>
  )
};


export default connect()(GetStarted);
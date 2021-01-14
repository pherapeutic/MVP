import React, {useState} from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  StyleSheet,
  Dimensions,
  Image,
  TextInput,
  ScrollView,
} from 'react-native';
import constants from '../../utils/constants';
import styles from './styles';
import SubmitButton from '../../components/submitButton';
import APICaller from '../../utils/APICaller';
import AsyncStorage from '@react-native-async-storage/async-storage';
import Events from '../../utils/events';
import {connect} from 'react-redux';
import {saveUser} from '../../redux/actions/user';
import AwesomeAlert from 'react-native-awesome-alerts';
import {validateEmail} from '../../utils/validateStrings';
import Header from '../../components/Header';

const {height, width} = Dimensions.get('window');

const AboutUs = (props) => {
  const {navigation} = props;

  return (
    <View style={styles.container}>
      <Image
        source={constants.images.background}
        resizeMode={'stretch'}
        style={styles.containerBackground}
      />

      <Header title="About" navigation={navigation} />

      <View style={styles.formView}>
        <View style={styles.formWrap}>
          <ScrollView
            style={{
              backgroundColor: constants.colors.white,
              paddingTop: height * 0.025,
              paddingBottom: height * 0.015,
              borderRadius: 10,
              marginRight: 10,
              marginLeft: 10,
              marginBottom: 5,
            }}
            showsVerticalScrollIndicator={false}>
            <Text
              style={{
                margin: 10,
                fontFamily: 'Poppins-Regular',
                color: 'grey',
              }}>
              Lorem Ipsum is simply dummy text of the printing and typesetting
              industry. Lorem Ipsum has been the industry's standard dummy text
              ever since the 1500s, when an unknown printer took a galley of
              type and scrambled it to make a type specimen book. It has
              survived not only five centuries, but also the leap into
              electronic typesetting, remaining essentially unchanged.{' '}
            </Text>

            <Text
              style={{
                margin: 10,
                fontFamily: 'Poppins-Regular',
                color: 'grey',
              }}>
              Lorem Ipsum is simply dummy text of the printing and typesetting
              industry. Lorem Ipsum has been the industry's standard dummy text
              ever since the 1500s, when an unknown printer took a galley of
              type and scrambled it to make a type specimen book. It has
              survived not only five centuries, but also the leap into
              electronic typesetting, remaining essentially unchanged.
            </Text>
            <Text
              style={{
                margin: 10,
                fontFamily: 'Poppins-Regular',
                color: 'grey',
              }}>
              Lorem Ipsum has been the industry's standard dummy text ever since
              the 1500s, when an unknown printer took a galley of type and
              scrambled it to make a type specimen book. It has survived not
              only five centuries, but also the leap into electronic
              typesetting, remaining essentially unchanged.
            </Text>
            <Text
              style={{
                margin: 10,
                fontFamily: 'Poppins-Regular',
                color: 'grey',
                marginBottom: 30,
              }}>
              Lorem Ipsum has been the industry's standard dummy text ever since
              the 1500s, when an unknown printer took a galley of type and
              scrambled it to make a type specimen book. It has survived not
              only five centuries, but also the leap into electronic
              typesetting, remaining essentially unchanged.
            </Text>
          </ScrollView>
        </View>
      </View>
    </View>
  );
};

export default AboutUs;

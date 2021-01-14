import React, { useEffect, useState } from 'react';
import {
    View,
    Text,
    TouchableOpacity,
    StyleSheet,
    Dimensions,
    Image,
    TextInput,
    Alert,
    FlatList,
    Switch,
} from 'react-native';
import Moment from 'moment';
import constants from '../../utils/constants';
import styles from './styles';
import LogoutAlert from '../../components/logoutAlert';
import APICaller from '../../utils/APICaller';
import { AirbnbRating } from 'react-native-ratings';

import { connect } from 'react-redux';
import Header from '../../components/Header';
import AppointmentHistoryModel from '../appointmentHistoryModel';

const { height, width } = Dimensions.get('window');
const PaymentHistory = (props) => {
    const [data, setData] = useState([]);
    const [showModal, setShowModal] = useState(false);

    const { navigation, userData, dispatch, userToken } = props;

    useEffect(() => {
        getList();
    }, []);
    const getList = () => {
        let headers = {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${userToken}`,

            
            Accept: 'application/json',
        };
        APICaller('getPaymentHistory', 'GET', null, headers)
            .then((response) => {
                setData(response['data']['data'])

            })
            .catch((error) => {
                console.log('error=> ', error);
            });

    }
    const momentfunc = (date) => {
        Moment.locale('en');
        var dt = date;
        return Moment(dt).format("LLL");
    }
    const alertBox = (message) => {
        Alert.alert(message
        );

    }


    return (
        <View style={styles.container}>
            <Image
                resizeMode={'stretch'}
                source={constants.images.background}
                style={styles.containerBackground}
            />

            <Header title="Payment History" navigation={navigation} />
            <View style={styles.content}>

                <FlatList
                    data={data}
                    style={{backgroundColor: 'rgba(52, 52, 52, 0)'}}
                    keyExtractor={(item, index) => {
                        return index.toString();
                    }}
                    renderItem={({ item }) => (
                        item == null ?
                            null
                            : <View style={styles.cardView}>

                         
                                <View style={styles.contentView}>
                                    <View style={{ flexDirection: "row" }}>
                                        <Text>{item.first_name} {item.last_name}</Text>
                                    </View>
                                    <View style={{ flexDirection: "row" }}>
                                        <Text style={{ fontSize: 11 }}>{momentfunc(item.created_at)}</Text>
                                    </View>
                                 
                                </View>
                                <View style={styles.paidView}>
                                    <View style={{ flexDirection: "row" }}><Text style={{ color: "rgb(20,20,20)", fontSize: 14 }}>{item.amount}</Text></View>
                                   
                                </View>
                            </View>
                    )}
                />
            </View>
      
        </View>


    );
};

const mapStateToProps = (state) => ({
    userData: state.user.userData,
    userToken: state.user.userToken,
});

export default connect(mapStateToProps)(PaymentHistory);

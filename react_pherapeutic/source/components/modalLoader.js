import {
	View,
	Dimensions,
	Modal,
	Text
} from 'react-native';
import React from 'react';
import { UIActivityIndicator } from 'react-native-indicators';
import constants from '../utils/constants';

const ModalLoader = (props) => {
	const { show, data } = props;
	const { label, backgroundColor } = data;
	return (
		<Modal
			transparent={show}
			visible={show}>
			<View style={{
				justifyContent: "center",
				alignItems: "center",
				backgroundColor: '#000000',
				opacity: 0.5,
				height: Dimensions.get("window").height,
				width: Dimensions.get("window").width,
			}}
			>
				<View style={{ justifyContent: "center", alignItems: "center", height: 100 }} >
					<UIActivityIndicator color={'rgb(191,54,160)'} />
				</View>
				{
					label
						?
						<View style={{ justifyContent: "center", alignItems: "center" }} >
							<Text style={{ color: "#ffffff", fontSize: 15, fontWeight: '500' }} >{label}</Text>
						</View>
						:
						<View />
				}
			</View>
		</Modal>
	)
}

export default ModalLoader;